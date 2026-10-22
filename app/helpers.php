<?php
declare(strict_types=1);

use YM\Core\Csrf;
use YM\Core\Env;
use YM\Core\Settings;

/** HTML-escape for output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Web path of the project root, e.g. "/YM" or "" (auto-detected). */
function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $configured = Env::get('APP_URL', '');
    if ($configured) {
        return $base = rtrim((string) parse_url($configured, PHP_URL_PATH), '/');
    }
    $docRoot = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $root = str_replace('\\', '/', YM_ROOT);
    $base = ($docRoot !== '' && stripos($root, $docRoot) === 0) ? substr($root, strlen($docRoot)) : '';
    return $base = rtrim($base, '/');
}

/** Scheme + host of the site. */
function site_origin(): string
{
    $configured = Env::get('APP_URL', '');
    if ($configured) {
        $p = parse_url($configured);
        return ($p['scheme'] ?? 'https') . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443';
    return ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function abs_url(string $path = ''): string
{
    return site_origin() . url($path);
}

/** Versioned URL for a file in /assets (cache-busting by mtime). */
function asset(string $path): string
{
    $file = YM_ROOT . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return url('assets/' . ltrim($path, '/')) . $v;
}

/** URL of an uploaded file, e.g. upload_url('projects', 'abc.jpg'). */
function upload_url(string $dir, ?string $file): string
{
    return $file ? url('ControlPanel/upload/' . $dir . '/' . rawurlencode($file)) : asset('img/placeholder.svg');
}

function admin_url(string $path = ''): string
{
    return url('ControlPanel/' . ltrim($path, '/'));
}

function redirect(string $to, int $code = 302): never
{
    header('Location: ' . $to, true, $code);
    exit;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

/** Flash messages: flash('success', 'Saved') sets one, flash() pulls all. */
function flash(?string $type = null, ?string $message = null): array
{
    if ($type !== null) {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
        return [];
    }
    $all = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $all;
}

/** Form values remembered across a validation redirect. */
function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function slugify(string $text): string
{
    $ascii = function_exists('iconv') ? @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) : false;
    $text = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $ascii !== false ? $ascii : $text), '-'));
    return $text !== '' ? substr($text, 0, 120) : 'item';
}

function excerpt(?string $html, int $limit = 160): string
{
    $text = trim((string) preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8')));
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $limit - 1)) . '…';
}

function reading_time(?string $html): int
{
    return max(1, (int) ceil(str_word_count(strip_tags((string) $html)) / 200));
}

function fmt_date(?string $date, string $format = 'j M Y'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

function time_ago(?string $date): string
{
    if (!$date) {
        return '';
    }
    $diff = time() - strtotime($date);
    return match (true) {
        $diff < 60 => 'just now',
        $diff < 3600 => floor($diff / 60) . ' min ago',
        $diff < 86400 => floor($diff / 3600) . ' h ago',
        $diff < 604800 => floor($diff / 86400) . ' d ago',
        default => fmt_date($date),
    };
}

function setting(string $key, string $default = ''): string
{
    return Settings::get($key, $default);
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Trimmed string from POST (falls back to GET). */
function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

/**
 * A safe same-site path from a ?next= value, or null. Blocks other hosts, protocol-relative URLs,
 * header injection and redirect loops back to the sign-in pages. $mustStartWith limits it to one area (e.g. the admin panel).
 */
function safe_next(?string $next, ?string $mustStartWith = null): ?string
{
    $next = (string) $next;
    if ($next === '' || $next[0] !== '/' || str_starts_with($next, '//') || str_contains($next, '\\') || preg_match('/[\x00-\x1f]/', $next)
        || preg_match('#(^|/)\.\.?(/|$|[?\#])#', $next) || preg_match('/%(2e|2f|5c)/i', $next)) {
        return null;
    }
    $base = base_path();
    if ($base !== '' && $next !== $base && !str_starts_with($next, $base . '/')) {
        return null;
    }
    if ($mustStartWith !== null && !str_starts_with($next, $mustStartWith)) {
        return null;
    }
    if (preg_match('#/(login|login-2fa|logout|forgot-password|reset-password)(\.php)?(\?|$)#', $next)) {
        return null;
    }
    return $next;
}

/**
 * Sends the redirect to the browser first, then runs $job (e.g. emailing subscribers) without making the admin wait.
 * Falls back to running the job inline when the connection cannot be closed early.
 */
function redirect_then(string $to, callable $job): never
{
    ignore_user_abort(true);
    @set_time_limit(120);
    session_write_close();
    header('Location: ' . $to, true, 302);
    header('Content-Length: 0');
    header('Connection: close');
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        flush();
    }
    try {
        $job();
    } catch (\Throwable $e) {
        error_log('Background job failed: ' . $e->getMessage());
    }
    exit;
}