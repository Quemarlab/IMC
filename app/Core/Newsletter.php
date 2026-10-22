<?php
declare(strict_types=1);

namespace YM\Core;

use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Subscribers;

/**
 * Emails subscribers when news or a project is published.
 *
 * A *campaign* is one announcement; a *delivery* row records each recipient, so sending can stop and resume
 * (time limits, the daily cap, a crash) without anyone getting the same email twice.
 * Campaigns are created when an item is saved as published with "email subscribers" ticked — or later, for a
 * scheduled article, when its date arrives (bin/newsletter.php runs every few minutes).
 */
final class Newsletter
{
    private const KINDS = ['news' => ['table' => 'posts', 'pref' => 'want_news', 'label' => 'article'], 'project' => ['table' => 'projects', 'pref' => 'want_projects', 'label' => 'project']];

    public static function enabled(): bool
    {
        return Settings::get('newsletter_enabled', '1') === '1' && Mailer::configured();
    }

    public static function dailyCap(): int
    {
        return max(1, (int) Settings::get('newsletter_daily_cap', '450'));
    }

    public static function sentToday(): int
    {
        return (int) DB::val("SELECT COUNT(*) FROM newsletter_deliveries WHERE status = 'sent' AND attempted_at >= CURDATE()");
    }

    // ------------------------------------------------------------------ campaigns
    public static function item(string $kind, int $id): ?array
    {
        return match ($kind) {
            'news' => Posts::find($id),
            'project' => Projects::find($id),
            default => null,
        };
    }

    private static function eligible(string $kind): string
    {
        return "status = 'active' AND " . self::KINDS[$kind]['pref'] . ' = 1';
    }

    /** Opens a campaign for an item and returns its id (null when the item is missing). */
    public static function createCampaign(string $kind, int $itemId, ?int $userId = null): ?int
    {
        $item = self::item($kind, $itemId);
        if (!$item) {
            return null;
        }
        $total = (int) DB::val('SELECT COUNT(*) FROM subscribers WHERE ' . self::eligible($kind));
        $subject = ($kind === 'news' ? 'New article: ' : 'New project: ') . $item['title'];
        $id = DB::insert('newsletter_campaigns', [
            'kind' => $kind,
            'item_id' => $itemId,
            'subject' => mb_substr($subject, 0, 190),
            'status' => $total > 0 ? 'sending' : 'done',
            'total' => $total,
            'created_by' => $userId,
            'finished_at' => $total > 0 ? null : date('Y-m-d H:i:s'),
        ]);
        Audit::log('newsletter', $kind, $itemId, $total . ' recipient(s)');
        return $id;
    }

    /** Turns published items that asked for a notification (and have not sent one) into campaigns. @return int campaigns created */
    public static function queueDue(): int
    {
        if (!self::enabled()) {
            return 0; // keep the request pending until mail is configured / the newsletter is switched on
        }
        $made = 0;
        foreach (['news' => "is_published = 1 AND (published_at IS NULL OR published_at <= NOW())", 'project' => 'is_published = 1'] as $kind => $cond) {
            $table = self::KINDS[$kind]['table'];
            foreach (DB::all("SELECT id FROM `$table` WHERE notify_subscribers = 1 AND notified_at IS NULL AND $cond") as $row) {
                DB::run("UPDATE `$table` SET notified_at = NOW() WHERE id = ? AND notified_at IS NULL", [$row['id']]);
                self::createCampaign($kind, (int) $row['id'], $_SESSION['uid'] ?? null);
                $made++;
            }
        }
        return $made;
    }

    public static function cancel(int $campaignId): void
    {
        DB::run("UPDATE newsletter_campaigns SET status = 'cancelled', finished_at = NOW() WHERE id = ? AND status = 'sending'", [$campaignId]);
    }

    /** Re-opens a finished campaign so recipients whose delivery failed are tried again. */
    public static function retryFailed(int $campaignId): void
    {
        DB::run("DELETE FROM newsletter_deliveries WHERE campaign_id = ? AND status = 'failed'", [$campaignId]);
        DB::run("UPDATE newsletter_campaigns SET status = 'sending', finished_at = NULL WHERE id = ? AND status = 'done'", [$campaignId]);
        self::recount($campaignId);
    }

    private static function recount(int $campaignId): void
    {
        DB::run(
            "UPDATE newsletter_campaigns SET sent = (SELECT COUNT(*) FROM newsletter_deliveries WHERE campaign_id = ? AND status = 'sent'),
                failed = (SELECT COUNT(*) FROM newsletter_deliveries WHERE campaign_id = ? AND status = 'failed') WHERE id = ?",
            [$campaignId, $campaignId, $campaignId]
        );
    }

    // ------------------------------------------------------------------ sending
    /**
     * Sends as much as the time budget and the daily cap allow. Safe to call from a request (after the response)
     * or from cron; concurrent calls are serialised with a database lock.
     * @return array{sent:int,failed:int,capped:bool,skipped:?string}
     */
    public static function process(int $seconds = 20): array
    {
        $out = ['sent' => 0, 'failed' => 0, 'capped' => false, 'skipped' => null];
        if (!self::enabled()) {
            $out['skipped'] = Mailer::configured() ? 'newsletter is switched off in Settings' : 'mail is not configured';
            return $out;
        }
        if (PHP_SAPI === 'cli' && !Env::get('APP_URL')) {
            $out['skipped'] = 'set APP_URL in .env so links in the emails point to the real site';
            return $out;
        }
        if (!(int) DB::val("SELECT GET_LOCK('ym_newsletter', 0)")) {
            $out['skipped'] = 'another run is already sending';
            return $out;
        }
        $deadline = microtime(true) + max(3, $seconds);
        try {
            foreach (DB::all("SELECT * FROM newsletter_campaigns WHERE status = 'sending' ORDER BY id") as $c) {
                $kind = $c['kind'];
                $item = self::item($kind, (int) $c['item_id']);
                if (!$item || !(int) $item['is_published']) {
                    DB::run("UPDATE newsletter_campaigns SET status = 'cancelled', finished_at = NOW() WHERE id = ?", [$c['id']]);
                    continue; // deleted or unpublished before it went out
                }
                $pendingFailures = [];
                $consecutive = 0;
                while (microtime(true) < $deadline) {
                    if (self::sentToday() >= self::dailyCap()) {
                        $out['capped'] = true;
                        self::recount((int) $c['id']);
                        break 2;
                    }
                    $batch = DB::all(
                        'SELECT s.* FROM subscribers s WHERE ' . self::eligible($kind) . ' AND NOT EXISTS
                         (SELECT 1 FROM newsletter_deliveries d WHERE d.campaign_id = ? AND d.subscriber_id = s.id) ORDER BY s.id LIMIT 20',
                        [$c['id']]
                    );
                    if (!$batch) {
                        break;
                    }
                    foreach ($batch as $sub) {
                        if (microtime(true) >= $deadline || self::sentToday() >= self::dailyCap()) {
                            break 2;
                        }
                        $ok = self::deliver($c, $item, $sub);
                        if ($ok) {
                            foreach ($pendingFailures as $fid) {
                                DB::run("INSERT IGNORE INTO newsletter_deliveries (campaign_id, subscriber_id, status) VALUES (?, ?, 'failed')", [$c['id'], $fid]);
                                $out['failed']++;
                            }
                            $pendingFailures = [];
                            $consecutive = 0;
                            DB::run("INSERT IGNORE INTO newsletter_deliveries (campaign_id, subscriber_id, status) VALUES (?, ?, 'sent')", [$c['id'], $sub['id']]);
                            $out['sent']++;
                        } else {
                            $pendingFailures[] = (int) $sub['id'];
                            if (++$consecutive >= 3) {
                                // Three in a row looks like an outage, not bad addresses: stop and retry these next run.
                                self::recount((int) $c['id']);
                                break 3;
                            }
                        }
                    }
                }
                foreach ($pendingFailures as $fid) {
                    DB::run("INSERT IGNORE INTO newsletter_deliveries (campaign_id, subscriber_id, status) VALUES (?, ?, 'failed')", [$c['id'], $fid]);
                    $out['failed']++;
                }
                self::recount((int) $c['id']);
                $left = (int) DB::val(
                    'SELECT COUNT(*) FROM subscribers s WHERE ' . self::eligible($kind) . ' AND NOT EXISTS
                     (SELECT 1 FROM newsletter_deliveries d WHERE d.campaign_id = ? AND d.subscriber_id = s.id)',
                    [$c['id']]
                );
                if ($left === 0) {
                    DB::run("UPDATE newsletter_campaigns SET status = 'done', finished_at = NOW() WHERE id = ?", [$c['id']]);
                }
            }
        } finally {
            Mailer::close();
            DB::run("SELECT RELEASE_LOCK('ym_newsletter')");
        }
        return $out;
    }

    private static function deliver(array $campaign, array $item, array $sub): bool
    {
        [$html, $footer, $headers] = self::compose($campaign['kind'], $item, $sub);
        return Mailer::send($sub['email'], $campaign['subject'], $html, null, null, [
            'headers' => $headers, 'reuse' => true, 'heading' => $item['title'], 'footer' => $footer,
        ]);
    }

    /** Sends one campaign email to a single address (admin "send me a copy"). */
    public static function sendTest(string $kind, int $itemId, string $email): bool
    {
        $item = self::item($kind, $itemId);
        if (!$item || !Mailer::configured()) {
            return false;
        }
        $sub = ['id' => 0, 'email' => $email, 'token' => str_repeat('0', 32)];
        [$html, $footer] = self::compose($kind, $item, $sub);
        return Mailer::send($email, '[Test] ' . ($kind === 'news' ? 'New article: ' : 'New project: ') . $item['title'], $html, null, null, ['heading' => $item['title'], 'footer' => $footer]);
    }

    /** @return array{0:string,1:string,2:array<string,string>} body html, footer html, headers */
    private static function compose(string $kind, array $item, array $sub): array
    {
        $isNews = $kind === 'news';
        $link = abs_url(($isNews ? 'news/' : 'projects/') . $item['slug']);
        $img = $item['cover_image'] ? site_origin() . upload_url($isNews ? 'news' : 'projects', $item['cover_image']) : '';
        $text = $isNews ? ($item['excerpt'] ?: excerpt($item['body'], 220)) : ($item['summary'] ?: excerpt($item['body'], 220));
        $meta = $isNews ? $item['category'] : trim($item['category'] . ($item['location'] ? ' · ' . $item['location'] : ''), ' ·');

        $html = ($img ? '<p style="margin:0 0 18px"><a href="' . e($link) . '"><img src="' . e($img) . '" alt="" width="544" style="max-width:100%;height:auto;border-radius:10px;display:block"></a></p>' : '')
            . ($meta !== '' ? '<p style="margin:0 0 8px;color:#a97d0b;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase">' . e($meta) . '</p>' : '')
            . '<p>' . e($text) . '</p>'
            . '<p style="margin:22px 0 4px"><a href="' . e($link) . '" style="background:#d4a017;color:#1d2124;padding:11px 20px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block">'
            . ($isNews ? 'Read the full article' : 'See the project') . '</a></p>';

        $manage = abs_url('unsubscribe?token=' . $sub['token']);
        $footer = '<p style="margin:26px 0 0;padding-top:16px;border-top:1px solid #eee;font-size:12px;color:#888">You are receiving this because you subscribed to updates from '
            . e(setting('company_name')) . '. <a href="' . e($manage) . '" style="color:#888">Manage preferences or unsubscribe</a>.</p>';
        $headers = ['List-Unsubscribe' => '<' . $manage . '>', 'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click'];
        return [$html, $footer, $headers];
    }
}
