</main>

<?php
use YM\Repositories\Services;

$footerServices = Services::latest(6);
$socials = ['linkedin' => 'bi-linkedin', 'facebook' => 'bi-facebook', 'twitter' => 'bi-twitter-x', 'instagram' => 'bi-instagram', 'youtube' => 'bi-youtube'];
?>
<section class="newsletter-strip">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <h3>Get mining safety &amp; compliance insights in your inbox</h3>
            </div>
            <div class="col-lg-6">
                <form class="js-ajax-form" action="<?= url('api/newsletter.php') ?>" method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="hp-field" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Your email address" aria-label="Email address" required>
                        <button class="btn btn-dark-ym" type="submit">Subscribe</button>
                    </div>
                    <div class="form-status mt-2" role="status"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <a class="footer-logo" href="<?= url() ?>"><img src="<?= asset('img/logo.png') ?>" alt="<?= e(setting('company_name')) ?>" loading="lazy"></a>
                <p><?= e(excerpt(setting('about_short'), 190)) ?></p>
                <div class="social-round mt-3">
                    <?php foreach ($socials as $key => $icon): if (setting($key)): ?>
                        <a href="<?= e(setting($key)) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($key)) ?>"><i class="bi <?= $icon ?>"></i></a>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h4>Company</h4>
                <ul>
                    <li><a href="<?= url('about') ?>">About us</a></li>
                    <li><a href="<?= url('projects') ?>">Projects</a></li>
                    <li><a href="<?= url('news') ?>">News</a></li>
                    <li><a href="<?= url('gallery') ?>">Gallery</a></li>
                    <li><a href="<?= url('contact') ?>">Contact</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h4>Services</h4>
                <ul>
                    <?php foreach ($footerServices as $s): ?>
                        <li><a href="<?= url('services/' . $s['slug']) ?>"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3">
                <h4>Get in touch</h4>
                <ul class="contact-list">
                    <li><i class="bi bi-geo-alt"></i><span><?= e(setting('address')) ?></span></li>
                    <li><i class="bi bi-telephone"></i><a href="tel:<?= e(preg_replace('/[^+\d]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li>
                    <li><i class="bi bi-envelope"></i><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li>
                    <li><i class="bi bi-clock"></i><span><?= e(setting('hours')) ?></span></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; <?= date('Y') ?> <?= e(setting('company_name')) ?>. All rights reserved.</span>
            <span><?= e(setting('tagline')) ?></span>
        </div>
    </div>
</footer>

<button class="to-top" id="toTop" type="button" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>

<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('vendor/aos/aos.js') ?>"></script>
<script src="<?= asset('vendor/glightbox/glightbox.min.js') ?>"></script>
<script src="<?= asset('js/site.js') ?>"></script>
</body>
</html>
