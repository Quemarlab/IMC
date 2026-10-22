<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use YM\Core\Csrf;
use YM\Core\Mailer;
use YM\Core\RateLimiter;
use YM\Repositories\Subscribers;

if (!is_post()) {
    json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
}
Csrf::check(true);

if (input('website') !== '') {
    json_response(['ok' => true, 'message' => 'Thanks for subscribing!']);
}
if (!RateLimiter::hit('newsletter:' . client_ip(), 5, 3600)) {
    json_response(['ok' => false, 'message' => 'Too many attempts. Please try again later.'], 429);
}

$email = strtolower(input('email'));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    json_response(['ok' => false, 'message' => 'Please enter a valid email address.'], 422);
}

// Double opt-in: nobody receives anything until they click the link we email them (stops people being signed up by others).
// If mail is not configured yet there is nothing to confirm with, so the subscription is activated directly.
$needsConfirmation = Mailer::configured();
$result = Subscribers::subscribe($email, $needsConfirmation);
$sub = $result['row'];

ignore_user_abort(true);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$json = json_encode(['ok' => true, 'message' => $needsConfirmation
    ? 'Almost done! We sent you an email — please click the confirmation link to finish subscribing.'
    : 'Thanks for subscribing!']);
header('Content-Length: ' . strlen($json));
header('Connection: close');
echo $json;
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    @ob_end_flush();
    flush();
}

if (!$needsConfirmation || !RateLimiter::hit('subscribe-mail:' . $email, 3, 3600)) {
    exit;
}

$company = e(setting('company_name'));
$manage = abs_url('unsubscribe?token=' . $sub['token']);
if ($sub['status'] === 'active') {
    // Already subscribed: say so (and give them the way to manage it) instead of silently doing nothing.
    Mailer::send($email, 'You are already subscribed',
        '<p>This address is already subscribed to updates from ' . $company . '.</p><p><a href="' . e($manage) . '">Manage your preferences or unsubscribe</a></p>');
} else {
    $confirm = abs_url('confirm-subscription?token=' . $sub['token']);
    Mailer::send($email, 'Confirm your subscription',
        '<p>Thanks for your interest in ' . $company . '. Please confirm your email address to start receiving news and project updates.</p>'
        . '<p style="margin:22px 0"><a href="' . e($confirm) . '" style="background:#d4a017;color:#1d2124;padding:11px 20px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block">Confirm my subscription</a></p>'
        . '<p style="font-size:13px;color:#888">If you did not ask for this, ignore this email — you will not be subscribed.</p>');
}
