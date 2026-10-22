<?php
http_response_code(404);
$page = ['title' => 'Page not found', 'robots' => 'noindex,follow'];
require __DIR__ . '/inc/header.php';
?>
<section class="section">
    <div class="container text-center" style="max-width:640px">
        <div class="display-title" style="font-size:7rem;color:var(--ym-gold);line-height:1">404</div>
        <h1 class="h2 mt-3">We couldn't find that page</h1>
        <p class="my-4">The link may be broken or the page may have moved. Try one of these instead:</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a class="btn btn-gold" href="<?= url() ?>">Homepage</a>
            <a class="btn btn-dark-ym" href="<?= url('services') ?>">Our services</a>
            <a class="btn btn-outline-dark" href="<?= url('contact') ?>">Contact us</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
