<?php
declare(strict_types=1);

namespace YM\Repositories;

use YM\Core\DB;

final class Subscribers
{
    /** Returns the subscriber row; $isNew tells the caller whether to send a welcome email. */
    public static function subscribe(string $email, bool &$isNew = false): array
    {
        $email = strtolower($email);
        $row = DB::one('SELECT * FROM subscribers WHERE email = ?', [$email]);
        if ($row) {
            $isNew = $row['status'] === 'unsubscribed';
            if ($isNew) {
                DB::update('subscribers', (int) $row['id'], ['status' => 'active']);
                $row['status'] = 'active';
            }
            return $row;
        }
        $token = bin2hex(random_bytes(16));
        $id = DB::insert('subscribers', ['email' => $email, 'token' => $token]);
        $isNew = true;
        return ['id' => $id, 'email' => $email, 'token' => $token, 'status' => 'active'];
    }

    public static function unsubscribe(string $token): bool
    {
        return DB::run("UPDATE subscribers SET status = 'unsubscribed' WHERE token = ?", [$token])->rowCount() > 0;
    }

    public static function delete(int $id): void
    {
        DB::run('DELETE FROM subscribers WHERE id = ?', [$id]);
    }

    public static function countActive(): int
    {
        return (int) DB::val("SELECT COUNT(*) FROM subscribers WHERE status = 'active'");
    }

    public static function paginate(string $q, int $page, int $per = 20): array
    {
        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where = 'email LIKE ?';
            $params[] = '%' . addcslashes($q, '%_\\') . '%';
        }
        $total = (int) DB::val("SELECT COUNT(*) FROM subscribers WHERE $where", $params);
        $pages = max(1, (int) ceil($total / $per));
        $page = max(1, min($page, $pages));
        $items = DB::all("SELECT * FROM subscribers WHERE $where ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);
        return compact('items', 'total', 'page', 'pages', 'per');
    }

    public static function all(): array
    {
        return DB::all("SELECT email, status, created_at FROM subscribers ORDER BY id DESC");
    }
}
