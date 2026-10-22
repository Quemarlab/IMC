<?php
$page = ['title' => 'Contact us', 'description' => 'Get in touch with Young Masters LTD for mining consultancy, OHS, environmental and training services in Rwanda.', 'nav' => 'contact'];
require __DIR__ . '/inc/header.php';

$hero = ['title' => 'Get in touch', 'lead' => 'Tell us about your operation — we usually reply within one working day.', 'crumbs' => [['Contact', null]]];
include __DIR__ . '/inc/page-hero.php';
$map = setting('map_embed');
?>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7" data-aos="fade-up">
                <div class="contact-card">
                    <h2 class="h3 mb-4">Send us a message</h2>
                    <form class="js-ajax-form" action="<?= url('api/contact.php') ?>" method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="hp-field" aria-hidden="true"><label>Leave empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="name">Full name *</label><input class="form-control" id="name" name="name" required maxlength="120" autocomplete="name"></div>
                            <div class="col-md-6"><label class="form-label" for="email">Email *</label><input class="form-control" type="email" id="email" name="email" required maxlength="190" autocomplete="email"></div>
                            <div class="col-md-6"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" maxlength="40" autocomplete="tel"></div>
                            <div class="col-md-6"><label class="form-label" for="topic">Service of interest</label><select class="form-select" id="topic" name="topic"><option value="">General enquiry</option>
                                <?php foreach (\YM\Repositories\Services::latest(50) as $s): ?><option<?= input('service') === $s['slug'] ? ' selected' : '' ?> value="<?= e($s['title']) ?>"><?= e($s['title']) ?></option><?php endforeach; ?></select></div>
                            <div class="col-12"><label class="form-label" for="subject">Subject</label><input class="form-control" id="subject" name="subject" maxlength="190" value="<?= e(input('subject')) ?>"></div>
                            <div class="col-12"><label class="form-label" for="body">How can we help? *</label><textarea class="form-control" id="body" name="body" rows="6" required minlength="10" maxlength="5000"></textarea></div>
                            <div class="col-12"><button class="btn btn-gold btn-lg" type="submit">Send message</button></div>
                            <div class="col-12"><div class="form-status" role="status"></div></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
                <span class="eyebrow">Contact details</span>
                <h2 class="h3 mt-3 mb-3">We'd love to hear from you</h2>
                <div class="info-item"><span class="ic"><i class="bi bi-geo-alt"></i></span><div><strong>Address</strong><?= e(setting('address')) ?></div></div>
                <div class="info-item"><span class="ic"><i class="bi bi-telephone"></i></span><div><strong>Phone</strong><a href="tel:<?= e(preg_replace('/[^+\d]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></div></div>
                <div class="info-item"><span class="ic"><i class="bi bi-envelope"></i></span><div><strong>Email</strong><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></div></div>
                <div class="info-item"><span class="ic"><i class="bi bi-clock"></i></span><div><strong>Working hours</strong><?= e(setting('hours')) ?></div></div>
            </div>
        </div>

        <?php if ($map && str_starts_with($map, 'https://www.google.com/maps/embed')): ?>
            <iframe class="map-frame mt-5" src="<?= e($map) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map showing our location"></iframe>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
