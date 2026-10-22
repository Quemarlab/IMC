<?php
declare(strict_types=1);

namespace YM\Repositories;

use YM\Core\DB;

final class Messages
{
    public const STATUSES = ['new', 'read', 'replied', 'archived'];

    public static function create(array $data): int
    {
        return DB::insert('messages', $data);
    }

    public static function find(int $id): ?array
    {
        return DB::one('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    public static function setStatus(int $id, string $status): void
    {
        if (in_array($status, self::STATUSES, true)) {
            DB::update('messages', $id, ['status' => $status]);
        }
    }

    public static function delete(int $id): void
    {
        DB::run('DELETE FROM messages WHERE id = ?', [$id]);
    }

    public static function latestId(): int
    {
        return (int) DB::val('SELECT COALESCE(MAX(id), 0) FROM messages');
    }

    /** Messages newer than $afterId, oldest first. */
    public static function since(int $afterId, int $limit = 20): array
    {
        return DB::all('SELECT * FROM messages WHERE id > ? ORDER BY id ASC LIMIT ' . (int) $limit, [$afterId]);
    }
    public static function unread(): int
    {
        return (int) DB::val("SELECT COUNT(*) FROM messages WHERE status = 'new'");
    }

    public static function counts(): array
    {
        $out = array_fill_keys(self::STATUSES, 0);
        foreach (DB::all('SELECT status, COUNT(*) AS c FROM messages GROUP BY status') as $r) {
            $out[$r['status']] = (int) $r['c'];
        }
        return $out;
    }

    public static function paginate(string $status, string $q, int $page, int $per = 15): array
    {
        $where = ['1=1'];
        $params = [];
        if (in_array($status, self::STATUSES, true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        } else {
            $where[] = "status <> 'archived'";
        }
        if ($q !== '') {
            $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ? OR body LIKE ?)';
            array_push($params, ...array_fill(0, 4, '%' . addcslashes($q, '%_\\') . '%'));
        }
        $w = implode(' AND ', $where);
        $total = (int) DB::val("SELECT COUNT(*) FROM messages WHERE $w", $params);
        $pages = max(1, (int) ceil($total / $per));
        $page = max(1, min($page, $pages));
        $items = DB::all("SELECT * FROM messages WHERE $w ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);
        return compact('items', 'total', 'page', 'pages', 'per');
    }

    /** Messages received per day for the last $days days (zero-filled). */
    public static function dailyCounts(int $days = 30): array
    {
        $rows = DB::all(
            'SELECT DATE(created_at) AS d, COUNT(*) AS c FROM messages WHERE created_at >= ? GROUP BY DATE(created_at)',
            [date('Y-m-d', strtotime('-' . ($days - 1) . ' days'))]
        );
        $map = array_column($rows, 'c', 'd');
        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $out[$d] = (int) ($map[$d] ?? 0);
        }
        return $out;
    }
}
