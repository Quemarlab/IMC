<?php
$page = ['title' => 'Unsubscribe', 'robots' => 'noindex,nofollow'];
require __DIR__ . '/inc/header.php';

use YM\Repositories\Subscribers;

$token = input('token');
$done = false;
if (is_post() && \YM\Core\Csrf::valid() && preg_match('/^[a-f0-9]{32}$/', $token)) {
    $done = Subscribers::unsubscribe($token);
}
?>
<section class="section">
    <div class="container text-center" style="max-width:560px">
        <?php if ($done): ?>
            <i class="bi bi-check-circle display-3 text-success"></i>
            <h1 class="h2 mt-3">You've been unsubscribed</h1>
            <p>You will no longer receive our newsletter. Sorry to see you go.</p>
            <a class="btn btn-dark-ym" href="<?= url() ?>">Back to the homepage</a>
        <?php else: ?>
            <h1 class="h2">Unsubscribe from our newsletter?</h1>
            <p>Confirm below and we'll stop sending you emails.</p>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>">
                <button class="btn btn-gold btn-lg">Yes, unsubscribe me</button></form>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
