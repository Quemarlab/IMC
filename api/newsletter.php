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

$isNew = false;
$sub = Subscribers::subscribe($email, $isNew);

ignore_user_abort(true);
header('Content-Type: application/json; charset=utf-8');
$json = json_encode(['ok' => true, 'message' => 'Thanks for subscribing! Please check your inbox.']);
header('Content-Length: ' . strlen($json));
header('Connection: close');
echo $json;
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    @ob_end_flush();
    flush();
}

if ($isNew) {
    $unsub = abs_url('unsubscribe?token=' . $sub['token']);
    Mailer::send(
        $email,
        'Welcome to ' . setting('company_name'),
        '<p>Thank you for subscribing. You will receive occasional updates on mining safety, environmental practice and compliance from our team.</p>'
        . '<p style="font-size:12px;color:#888">Changed your mind? <a href="' . e($unsub) . '">Unsubscribe</a> at any time.</p>'
    );
}
