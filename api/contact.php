<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use YM\Core\Csrf;
use YM\Core\Env;
use YM\Core\Mailer;
use YM\Core\RateLimiter;
use YM\Core\Validator;
use YM\Repositories\Messages;

if (!is_post()) {
    json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
}
Csrf::check(true);

// Honeypot: bots fill the hidden field. Pretend success so they don't adapt.
if (input('website') !== '') {
    json_response(['ok' => true, 'message' => 'Thank you — your message has been sent.']);
}

if (!RateLimiter::hit('contact:' . client_ip(), 5, 3600)) {
    json_response(['ok' => false, 'message' => 'You have sent several messages recently. Please try again later.'], 429);
}

$v = new Validator($_POST, [
    'name' => 'required|max:120',
    'email' => 'required|email|max:190',
    'phone' => 'max:40',
    'subject' => 'max:190',
    'body' => 'required|min:10|max:5000',
]);
if ($v->fails()) {
    json_response(['ok' => false, 'message' => $v->first()], 422);
}
$d = $v->data();

// Store first: a lead must never be lost because SMTP is down.
Messages::create($d + ['ip' => client_ip()]);

// Reply to the visitor now, then send the notification email without making them wait.
ignore_user_abort(true);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$json = json_encode(['ok' => true, 'message' => 'Thank you — your message has been sent. We will get back to you shortly.']);
header('Content-Length: ' . strlen($json));
header('Connection: close');
echo $json;
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    @ob_end_flush();
    flush();
}

$to = Env::get('MAIL_TO', setting('email'));
$rows = [
    'From' => $d['name'] . ' <' . $d['email'] . '>',
    'Phone' => $d['phone'] ?: '—',
    'Subject' => $d['subject'] ?: '—',
];
$html = '<p>You received a new message through the website contact form.</p><table cellpadding="6" style="border-collapse:collapse">';
foreach ($rows as $k => $val) {
    $html .= '<tr><td style="color:#777">' . e($k) . '</td><td><strong>' . e($val) . '</strong></td></tr>';
}
$html .= '</table><p style="white-space:pre-wrap;background:#f6f5f0;padding:14px;border-radius:8px">' . e($d['body']) . '</p>'
    . '<p><a href="' . e(abs_url('ControlPanel/messages')) . '">Open in the admin panel</a></p>';
Mailer::send($to, 'New website enquiry from ' . $d['name'], $html, $d['email']);
