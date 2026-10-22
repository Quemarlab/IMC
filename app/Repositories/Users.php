<?php
declare(strict_types=1);

namespace YM\Repositories;

use YM\Core\DB;

final class Users
{
    public static function all(): array
    {
        return DB::all('SELECT id, name, email, role, status, last_login_at, created_at FROM users ORDER BY id');
    }

    public static function find(int $id): ?array
    {
        return DB::one('SELECT id, name, email, role, status, last_login_at, created_at FROM users WHERE id = ?', [$id]);
    }

    public static function emailTaken(string $email, int $ignoreId = 0): bool
    {
        return (bool) DB::val('SELECT id FROM users WHERE email = ? AND id <> ?', [strtolower($email), $ignoreId]);
    }

    public static function create(string $name, string $email, string $password, string $role): int
    {
        return DB::insert('users', [
            'name' => $name,
            'email' => strtolower($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);
    }

    /** Active admins left in the system: used to stop the last one being demoted/disabled/deleted. */
    public static function activeAdmins(int $excludeId = 0): int
    {
        return (int) DB::val("SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active' AND id <> ?", [$excludeId]);
    }

    public static function delete(int $id): void
    {
        DB::run('DELETE FROM users WHERE id = ?', [$id]);
    }
}
