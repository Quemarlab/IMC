<?php
declare(strict_types=1);

namespace YM\Core;

/**
 * Session authentication with server-side brute-force protection
 * (the old cookie-based counter and "remember me" cookie are gone: both were forgeable).
 */
final class Auth
{
    private const MAX_FAILURES = 5;
    private const LOCK_MINUTES = 15;
    private const IDLE_SECONDS = 7200;

    private static ?array $user = null;

    /** @return array{ok:bool,message:string} */
    public static function attempt(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        $generic = 'Invalid email or password.';

        if (self::isLocked($email)) {
            return ['ok' => false, 'message' => 'Too many failed attempts. Try again in ' . self::LOCK_MINUTES . ' minutes.'];
        }

        $user = DB::one('SELECT * FROM users WHERE email = ?', [$email]);
        // Always run a hash comparison so response time doesn't reveal whether the account exists.
        $hash = $user['password_hash'] ?? '$2y$10$PaTj27AxQESxiK6eO/R/nuAdhQhxr832y9./IPiBU1.8vQ1pFOoZe';
        $valid = password_verify($password, $hash) && $user !== null;

        if (!$valid) {
            DB::insert('login_attempts', ['email' => $email, 'ip' => client_ip()]);
            Audit::log('login.failed', 'user', null, $email);
            return ['ok' => false, 'message' => $generic];
        }
        if ($user['status'] !== 'active') {
            return ['ok' => false, 'message' => 'This account is disabled. Contact an administrator.'];
        }

        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            DB::update('users', (int) $user['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        session_regenerate_id(true);
        $_SESSION['uid'] = (int) $user['id'];
        $_SESSION['last_seen'] = time();
        DB::update('users', (int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        DB::run('DELETE FROM login_attempts WHERE email = ?', [$email]);
        Audit::log('login.success', 'user', (int) $user['id']);
        return ['ok' => true, 'message' => 'OK'];
    }

    private static function isLocked(string $email): bool
    {
        $since = date('Y-m-d H:i:s', time() - self::LOCK_MINUTES * 60);
        $byEmail = (int) DB::val('SELECT COUNT(*) FROM login_attempts WHERE email = ? AND created_at > ?', [$email, $since]);
        $byIp = (int) DB::val('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND created_at > ?', [client_ip(), $since]);
        return $byEmail >= self::MAX_FAILURES || $byIp >= self::MAX_FAILURES * 3;
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }
        if (empty($_SESSION['uid'])) {
            return null;
        }
        if (time() - ($_SESSION['last_seen'] ?? 0) > self::IDLE_SECONDS) {
            self::logout();
            return null;
        }
        $_SESSION['last_seen'] = time();
        $user = DB::one('SELECT id, name, email, role, status FROM users WHERE id = ?', [$_SESSION['uid']]);
        if (!$user || $user['status'] !== 'active') {
            self::logout();
            return null;
        }
        return self::$user = $user;
    }

    /** Is there a live session? Unlike user(), does not extend the idle timeout (used by background polling). */
    public static function peek(): bool
    {
        return !empty($_SESSION['uid']) && time() - ($_SESSION['last_seen'] ?? 0) <= self::IDLE_SECONDS;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function is(string ...$roles): bool
    {
        $u = self::user();
        return $u !== null && in_array($u['role'], $roles, true);
    }

    /** Gate for admin pages; optionally restricted to roles. */
    public static function require(string ...$roles): array
    {
        $user = self::user();
        if ($user === null) {
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? null;
            redirect(admin_url('login'));
        }
        if ($roles && !in_array($user['role'], $roles, true)) {
            http_response_code(403);
            exit('You do not have permission to view this page.');
        }
        return $user;
    }

    public static function logout(): void
    {
        $id = $_SESSION['uid'] ?? null;
        if ($id) {
            Audit::log('logout', 'user', (int) $id);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'] ?? '', $p['secure'], $p['httponly']);
        }
        session_destroy();
        self::$user = null;
    }

    /** Creates a reset token (returned raw, stored hashed). Null when the email is unknown. */
    public static function createResetToken(string $email): ?array
    {
        $user = DB::one("SELECT id, name, email FROM users WHERE email = ? AND status = 'active'", [strtolower(trim($email))]);
        if (!$user) {
            return null;
        }
        $token = bin2hex(random_bytes(32));
        DB::insert('password_resets', [
            'user_id' => $user['id'],
            'token_hash' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ]);
        return ['user' => $user, 'token' => $token];
    }

    public static function resetUserFromToken(string $token): ?array
    {
        return DB::one(
            'SELECT r.id AS reset_id, u.id, u.email FROM password_resets r JOIN users u ON u.id = r.user_id
             WHERE r.token_hash = ? AND r.used_at IS NULL AND r.expires_at > NOW()',
            [hash('sha256', $token)]
        );
    }

    public static function resetPassword(string $token, string $newPassword): bool
    {
        $row = self::resetUserFromToken($token);
        if (!$row) {
            return false;
        }
        DB::update('users', (int) $row['id'], ['password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)]);
        DB::run('UPDATE password_resets SET used_at = NOW() WHERE user_id = ?', [$row['id']]);
        DB::run('DELETE FROM login_attempts WHERE email = ?', [$row['email']]);
        Audit::log('password.reset', 'user', (int) $row['id']);
        return true;
    }

    /** Minimum policy shared by every password form. Returns an error or null. */
    public static function passwordError(string $password): ?string
    {
        if (strlen($password) < 10) {
            return 'Password must be at least 10 characters.';
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            return 'Password must contain letters and numbers.';
        }
        return null;
    }
}
