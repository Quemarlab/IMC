<?php
$page = ['title' => 'Confirm subscription', 'robots' => 'noindex,nofollow'];
require __DIR__ . '/inc/header.php';

use YM\Core\RateLimiter;
use YM\Repositories\Subscribers;

$sub = RateLimiter::hit('confirm:' . client_ip(), 30, 3600) ? Subscribers::confirm(input('token')) : null;
?>
<section class="section">
    <div class="container text-center" style="max-width:560px">
        <?php if ($sub): ?>
            <i class="bi bi-check-circle display-3 text-success"></i>
            <h1 class="h2 mt-3">You're subscribed</h1>
            <p>Thank you! We'll email you when we publish new articles and projects. You can choose what you receive, or stop at any time, from the link at the bottom of every email.</p>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a class="btn btn-gold" href="<?= url('news') ?>">Read our latest news</a>
                <a class="btn btn-outline-dark" href="<?= url('unsubscribe?token=' . e($sub['token'])) ?>">Choose what I receive</a>
            </div>
        <?php else: ?>
            <i class="bi bi-link-45deg display-3 text-body-secondary"></i>
            <h1 class="h2 mt-3">This link is not valid</h1>
            <p>It may be incomplete or too old. Please subscribe again from the form at the bottom of any page.</p>
            <a class="btn btn-dark-ym" href="<?= url() ?>">Back to the homepage</a>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
