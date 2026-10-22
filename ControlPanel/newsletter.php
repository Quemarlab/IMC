<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\DB;
use YM\Core\Mailer;
use YM\Core\Newsletter;
use YM\Repositories\Subscribers;

$me = Auth::require();

if (is_post()) {
    admin_post();
    $id = (int) input('id');
    $c = $id ? DB::one('SELECT * FROM newsletter_campaigns WHERE id = ?', [$id]) : null;
    switch (input('op')) {
        case 'process':
            redirect_then(admin_url('newsletter'), static function () { Newsletter::queueDue(); Newsletter::process(25); });
        case 'cancel':
            if ($c) {
                Newsletter::cancel($id);
                Audit::log('newsletter.cancel', $c['kind'], (int) $c['item_id']);
                flash('success', 'Sending was stopped. People who already received it are unaffected.');
            }
            break;
        case 'retry':
            if ($c) {
                Newsletter::retryFailed($id);
                flash('success', 'Failed recipients will be tried again.');
                redirect_then(admin_url('newsletter'), static function () { Newsletter::process(25); });
            }
            break;
        case 'test':
            $ok = $c && Newsletter::sendTest($c['kind'], (int) $c['item_id'], $me['email']);
            flash($ok ? 'success' : 'error', $ok ? 'A copy was emailed to ' . $me['email'] . '.' : 'The email could not be sent. Check the mail settings in .env.');
            break;
        case 'resend':
            if ($c && Newsletter::item($c['kind'], (int) $c['item_id'])) {
                Newsletter::createCampaign($c['kind'], (int) $c['item_id'], (int) $me['id']);
                flash('success', 'A new send was started for everyone currently subscribed.');
                redirect_then(admin_url('newsletter'), static function () { Newsletter::process(25); });
            }
            break;
    }
    redirect(admin_url('newsletter'));
}

$campaigns = DB::all('SELECT * FROM newsletter_campaigns ORDER BY id DESC LIMIT 50');
$counts = Subscribers::counts();
$enabled = Newsletter::enabled();
$pending = (int) DB::val("SELECT COUNT(*) FROM posts WHERE notify_subscribers = 1 AND notified_at IS NULL AND is_published = 1 AND published_at > NOW()");
$todaySent = Newsletter::sentToday();
$cap = Newsletter::dailyCap();

admin_header('Newsletter', 'newsletter');
?>
<?php if (!Mailer::configured()): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i>Mail is not configured (<code>MAIL_USER</code> / <code>MAIL_PASS</code> in <code>.env</code>), so no emails can go out and new subscribers are activated without confirmation.</div>
<?php elseif (!$enabled): ?>
    <div class="alert alert-warning"><i class="bi bi-pause-circle me-1"></i>Newsletter sending is switched off in <a href="<?= admin_url('settings') ?>">Settings</a>.</div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card stat-card"><span class="ic" style="--c:#2f7d32"><i class="bi bi-people"></i></span><div><div class="num"><?= $counts['active'] ?></div><div class="lbl">Active subscribers</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card stat-card"><span class="ic" style="--c:#d4a017"><i class="bi bi-hourglass-split"></i></span><div><div class="num"><?= $counts['pending'] ?></div><div class="lbl">Waiting to confirm</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card stat-card"><span class="ic" style="--c:#2b6cb0"><i class="bi bi-send"></i></span><div><div class="num"><?= $todaySent ?> / <?= $cap ?></div><div class="lbl">Sent today / daily limit</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card stat-card"><span class="ic" style="--c:#7b61c4"><i class="bi bi-calendar-event"></i></span><div><div class="num"><?= $pending ?></div><div class="lbl">Scheduled articles to announce</div></div></div></div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Sent announcements</span>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="op" value="process"><button class="btn btn-sm btn-light"<?= $enabled ? '' : ' disabled' ?>><i class="bi bi-arrow-repeat me-1"></i>Send pending now</button></form>
    </div>
    <?php if (!$campaigns): ?>
        <div class="empty-admin"><i class="bi bi-megaphone"></i>Nothing sent yet. Publish a news article or project with “Email subscribers when published” ticked and it appears here.</div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-hover">
            <thead><tr><th>Announcement</th><th>Progress</th><th>Status</th><th>Started</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($campaigns as $c):
                $item = Newsletter::item($c['kind'], (int) $c['item_id']);
                $done = (int) $c['sent'] + (int) $c['failed'];
                $pct = $c['total'] > 0 ? min(100, round($done / $c['total'] * 100)) : 100;
                $badge = ['sending' => 'info', 'done' => 'success', 'cancelled' => 'secondary'][$c['status']]; ?>
                <tr>
                    <td><span class="badge text-bg-light me-1"><?= $c['kind'] === 'news' ? 'News' : 'Project' ?></span><strong><?= e($c['subject']) ?></strong>
                        <?php if ($item): ?><div class="small"><a href="<?= e(url(($c['kind'] === 'news' ? 'news/' : 'projects/') . $item['slug'])) ?>" target="_blank">View on website</a></div><?php endif; ?></td>
                    <td style="min-width:170px"><div class="progress" style="height:6px"><div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div></div>
                        <div class="small text-body-secondary mt-1"><?= (int) $c['sent'] ?> sent of <?= (int) $c['total'] ?><?= $c['failed'] ? ' · <span class="text-danger">' . (int) $c['failed'] . ' failed</span>' : '' ?></div></td>
                    <td><span class="badge text-bg-<?= $badge ?>"><?= e(ucfirst($c['status'])) ?></span></td>
                    <td class="small text-body-secondary text-nowrap"><?= e(time_ago($c['created_at'])) ?></td>
                    <td class="text-end text-nowrap">
                        <?php foreach ([['test', 'bi-envelope', 'Email me a copy', true], ['retry', 'bi-arrow-clockwise', 'Retry failed', $c['failed'] > 0 && $c['status'] !== 'cancelled'], ['cancel', 'bi-x-octagon', 'Stop sending', $c['status'] === 'sending'], ['resend', 'bi-megaphone', 'Send again to everyone', $c['status'] !== 'sending' && $item]] as [$op, $icon, $tip, $show]): if (!$show) continue; ?>
                            <form class="d-inline" method="post"<?= $op === 'resend' ? ' data-confirm="Send this to all current subscribers again?" data-confirm-text="People who already got it will receive it a second time."' : '' ?>><?= csrf_field() ?><input type="hidden" name="op" value="<?= $op ?>"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button class="btn btn-sm btn-light" title="<?= e($tip) ?>"><i class="bi <?= $icon ?>"></i></button></form>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>
<p class="small text-body-secondary mt-3">Emails go out automatically when a news article or project is published (or on the scheduled date). Every email carries a personal link to manage preferences or unsubscribe, and mail apps show a one-click unsubscribe button.
    Large lists are sent in batches within the daily limit; the scheduled task <code>bin/newsletter.php</code> keeps going between visits.</p>
<?php admin_footer();
