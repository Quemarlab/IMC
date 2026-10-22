<?php
declare(strict_types=1);

namespace YM\Repositories;

use YM\Core\DB;

/**
 * Newsletter subscribers. Lifecycle: pending (waiting for the confirmation email link) → active → unsubscribed.
 * Each subscriber has a private token used in every link (confirm, preferences, unsubscribe).
 */
final class Subscribers
{
    public const STATUSES = ['pending', 'active', 'unsubscribed'];

    public static function findByEmail(string $email): ?array
    {
        return DB::one('SELECT * FROM subscribers WHERE email = ?', [strtolower($email)]);
    }

    public static function findByToken(string $token): ?array
    {
        return preg_match('/^[a-f0-9]{32}$/', $token) ? DB::one('SELECT * FROM subscribers WHERE token = ?', [$token]) : null;
    }

    public static function find(int $id): ?array
    {
        return DB::one('SELECT * FROM subscribers WHERE id = ?', [$id]);
    }

    /**
     * Start (or restart) a subscription. With $needsConfirmation the person must click the emailed link before
     * anything is sent to them; without it (mail not configured) they are active immediately.
     * @return array{row:array,action:string} action: 'new' | 'pending' (confirmation must be re-sent) | 'active' (already subscribed) | 'resubscribed'
     */
    public static function subscribe(string $email, bool $needsConfirmation): array
    {
        $email = strtolower($email);
        $row = self::findByEmail($email);
        $status = $needsConfirmation ? 'pending' : 'active';
        $confirmed = $needsConfirmation ? null : date('Y-m-d H:i:s');

        if (!$row) {
            $id = DB::insert('subscribers', ['email' => $email, 'token' => bin2hex(random_bytes(16)), 'status' => $status, 'confirmed_at' => $confirmed]);
            return ['row' => self::find($id), 'action' => 'new'];
        }
        if ($row['status'] === 'active') {
            return ['row' => $row, 'action' => 'active'];
        }
        if ($row['status'] === 'unsubscribed') {
            DB::update('subscribers', (int) $row['id'], ['status' => $status, 'confirmed_at' => $confirmed, 'unsubscribed_at' => null]);
            return ['row' => self::find((int) $row['id']), 'action' => $needsConfirmation ? 'pending' : 'resubscribed'];
        }
        return ['row' => $row, 'action' => 'pending'];
    }

    /** Confirms a pending subscription. Returns the subscriber, or null for an unknown token. */
    public static function confirm(string $token): ?array
    {
        $row = self::findByToken($token);
        if (!$row) {
            return null;
        }
        if ($row['status'] !== 'active') {
            DB::update('subscribers', (int) $row['id'], ['status' => 'active', 'confirmed_at' => date('Y-m-d H:i:s'), 'unsubscribed_at' => null]);
            $row = self::find((int) $row['id']);
        }
        return $row;
    }

    public static function unsubscribe(string $token): bool
    {
        $row = self::findByToken($token);
        if (!$row) {
            return false;
        }
        if ($row['status'] !== 'unsubscribed') {
            DB::update('subscribers', (int) $row['id'], ['status' => 'unsubscribed', 'unsubscribed_at' => date('Y-m-d H:i:s')]);
        }
        return true;
    }

    public static function setPreferences(string $token, bool $news, bool $projects): bool
    {
        $row = self::findByToken($token);
        if (!$row) {
            return false;
        }
        DB::update('subscribers', (int) $row['id'], ['want_news' => (int) $news, 'want_projects' => (int) $projects]);
        return true;
    }

    public static function delete(int $id): void
    {
        DB::run('DELETE FROM subscribers WHERE id = ?', [$id]);
    }

    public static function countActive(): int
    {
        return (int) DB::val("SELECT COUNT(*) FROM subscribers WHERE status = 'active'");
    }

    /** @return array<string,int> status => count (all statuses present) */
    public static function counts(): array
    {
        $out = array_fill_keys(self::STATUSES, 0);
        foreach (DB::all('SELECT status, COUNT(*) AS c FROM subscribers GROUP BY status') as $r) {
            $out[$r['status']] = (int) $r['c'];
        }
        return $out;
    }

    public static function paginate(string $q, int $page, int $per = 20, string $status = ''): array
    {
        $where = ['1=1'];
        $params = [];
        if ($q !== '') {
            $where[] = 'email LIKE ?';
            $params[] = '%' . addcslashes($q, '%_\\') . '%';
        }
        if (in_array($status, self::STATUSES, true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        $w = implode(' AND ', $where);
        $total = (int) DB::val("SELECT COUNT(*) FROM subscribers WHERE $w", $params);
        $pages = max(1, (int) ceil($total / $per));
        $page = max(1, min($page, $pages));
        $items = DB::all("SELECT * FROM subscribers WHERE $w ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);
        return compact('items', 'total', 'page', 'pages', 'per');
    }

    public static function all(): array
    {
        return DB::all('SELECT email, status, want_news, want_projects, created_at, confirmed_at, unsubscribed_at FROM subscribers ORDER BY id DESC');
    }
}
