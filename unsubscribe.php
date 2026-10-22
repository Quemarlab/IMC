<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

use YM\Core\Csrf;
use YM\Core\Mailer;
use YM\Core\RateLimiter;
use YM\Repositories\Subscribers;

$token = input('token');

// RFC 8058 one-click unsubscribe: mail apps (Gmail, Apple Mail …) POST here directly from the "Unsubscribe" button next to the sender.
if (is_post() && ($_POST['List-Unsubscribe'] ?? '') === 'One-Click') {
    $ok = RateLimiter::hit('oneclick:' . client_ip(), 60, 3600) && Subscribers::unsubscribe($token);
    http_response_code($ok ? 200 : 400);
    header('Content-Type: text/plain; charset=utf-8');
    exit($ok ? 'Unsubscribed' : 'Invalid request');
}

$sub = Subscribers::findByToken($token);
$notice = null;

if (is_post() && Csrf::valid()) {
    $op = input('op');
    if ($sub && $op === 'unsubscribe') {
        Subscribers::unsubscribe($token);
        $notice = 'unsubscribed';
    } elseif ($sub && $op === 'prefs' && $sub['status'] === 'active') {
        $news = isset($_POST['want_news']);
        $projects = isset($_POST['want_projects']);
        if (!$news && !$projects) {
            Subscribers::unsubscribe($token); // nothing left to receive
            $notice = 'unsubscribed';
        } else {
            Subscribers::setPreferences($token, $news, $projects);
            $notice = 'saved';
        }
    } elseif ($sub && $op === 'resubscribe') {
        Subscribers::confirm($token); // they hold their private link, so this is their own decision
        $notice = 'resubscribed';
    } elseif (!$sub && $op === 'lost' && filter_var(input('email'), FILTER_VALIDATE_EMAIL)) {
        // "I lost the link": email the manage link to the address, never confirm whether it is subscribed.
        $email = strtolower(input('email'));
        if (RateLimiter::hit('lostlink:' . client_ip(), 5, 3600) && RateLimiter::hit('lostlink-mail:' . $email, 2, 3600)) {
            $row = Subscribers::findByEmail($email);
            if ($row && Mailer::configured()) {
                $url = abs_url('unsubscribe?token=' . $row['token']);
                Mailer::send($email, 'Manage your subscription',
                    '<p>You asked to manage your subscription to ' . e(setting('company_name')) . ' updates.</p><p><a href="' . e($url) . '">Manage preferences or unsubscribe</a></p>'
                    . '<p style="font-size:13px;color:#888">If you did not ask for this, ignore this email.</p>');
            }
        }
        $notice = 'lost-sent';
    }
    $sub = Subscribers::findByToken($token);
}

$page = ['title' => 'Email preferences', 'robots' => 'noindex,nofollow'];
require __DIR__ . '/inc/header.php';
?>
<section class="section">
    <div class="container" style="max-width:600px">
    <?php if ($notice === 'lost-sent'): ?>
        <div class="text-center"><i class="bi bi-envelope-check display-3 text-success"></i>
            <h1 class="h2 mt-3">Check your inbox</h1>
            <p>If that address is subscribed, we've sent it a link to manage the subscription.</p></div>

    <?php elseif (!$sub): ?>
        <div class="text-center">
            <h1 class="h2">Manage your email updates</h1>
            <p>Every email we send has a personal link at the bottom. If you can't find it, enter your address and we'll send you a new one.</p>
            <form method="post" class="d-flex gap-2 justify-content-center flex-wrap"><?= csrf_field() ?><input type="hidden" name="op" value="lost">
                <input class="form-control" style="max-width:320px" type="email" name="email" placeholder="you@example.com" required maxlength="190" aria-label="Your email address">
                <button class="btn btn-gold">Send me the link</button></form>
        </div>

    <?php elseif ($sub['status'] === 'unsubscribed'): ?>
        <div class="text-center"><i class="bi bi-check-circle display-3 text-success"></i>
            <h1 class="h2 mt-3">You've been unsubscribed</h1>
            <p>You will no longer receive our newsletter. Sorry to see you go.</p>
            <form method="post" class="mb-3"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="op" value="resubscribe">
                <span class="text-body-secondary">Changed your mind?</span> <button class="btn btn-link p-0 align-baseline">Subscribe again</button></form>
            <a class="btn btn-dark-ym" href="<?= url() ?>">Back to the homepage</a></div>

    <?php elseif ($sub['status'] === 'pending'): ?>
        <div class="text-center"><i class="bi bi-hourglass-split display-3 text-body-secondary"></i>
            <h1 class="h2 mt-3">Confirm your subscription</h1>
            <p>This address hasn't been confirmed yet. Click the link in the email we sent, or confirm here.</p>
            <a class="btn btn-gold" href="<?= url('confirm-subscription?token=' . e($token)) ?>">Confirm my subscription</a></div>

    <?php else: ?>
        <h1 class="h2 text-center">Your email updates</h1>
        <p class="text-center text-body-secondary"><?= e($sub['email']) ?></p>
        <?php if ($notice === 'saved' || $notice === 'resubscribed'): ?><div class="alert alert-success text-center"><?= $notice === 'saved' ? 'Your preferences were saved.' : 'Welcome back — you are subscribed again.' ?></div><?php endif; ?>
        <form method="post" class="contact-card mb-4"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="op" value="prefs">
            <h2 class="h5 mb-3">Send me emails about</h2>
            <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" role="switch" id="want_news" name="want_news" value="1"<?= $sub['want_news'] ? ' checked' : '' ?>><label class="form-check-label" for="want_news">New articles and news</label></div>
            <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" role="switch" id="want_projects" name="want_projects" value="1"<?= $sub['want_projects'] ? ' checked' : '' ?>><label class="form-check-label" for="want_projects">New projects</label></div>
            <button class="btn btn-gold">Save preferences</button>
            <div class="form-text mt-2">Switch both off to stop all emails.</div>
        </form>
        <form method="post" class="text-center"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="op" value="unsubscribe">
            <button class="btn btn-outline-dark">Unsubscribe from everything</button></form>
    <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
