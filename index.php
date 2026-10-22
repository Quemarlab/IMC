<?php
$page = ['nav' => 'home'];
require __DIR__ . '/inc/header.php';

use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Services;

$services = Services::latest(6);
$projects = Projects::latest(3);
$posts = Posts::latest(3);

$steps = [
    ['Assess', 'We start on site: understanding your operation, hazards, obligations and the people doing the work.'],
    ['Plan', 'We turn findings into a clear, prioritised action plan with owners, timelines and measurable targets.'],
    ['Train & implement', 'Hands-on training and technical support put the plan into everyday practice with your team.'],
    ['Monitor & improve', 'We track results, support audits and inspections, and keep improving as your operation evolves.'],
];
?>

<section class="hero" style="background-image:url('<?= asset('img/hero-training.jpg') ?>')">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <span class="eyebrow mb-3" data-aos="fade-up">Mining · OHS · Environment</span>
                <h1 data-aos="fade-up" data-aos-delay="80">Safer mines. <em>Stronger compliance.</em> Skilled people.</h1>
                <p class="lead mt-4" data-aos="fade-up" data-aos-delay="160">
                    <?= e(setting('company_name')) ?> is a Rwanda-based mining consultancy and training company helping operators, cooperatives and institutions achieve safe, productive and environmentally sustainable mining.
                </p>
                <div class="d-flex flex-wrap gap-3 mt-4" data-aos="fade-up" data-aos-delay="240">
                    <a href="<?= url('contact') ?>" class="btn btn-gold btn-lg">Request a consultation</a>
                    <a href="<?= url('services') ?>" class="btn btn-outline-light-ym btn-lg">Explore our services</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hero-pillars">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="pillar-card" style="--c:#4a5258">
                    <span class="ic"><i class="bi bi-gem"></i></span>
                    <div><h3>Mining expertise</h3><p>Geology, mine planning and compliance grounded in Rwanda's mining reality.</p></div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="pillar-card" style="--c:#d4a017">
                    <span class="ic"><i class="bi bi-shield-check"></i></span>
                    <div><h3>Health &amp; safety first</h3><p>Practical OHS systems and training that protect workers and keep sites productive.</p></div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="pillar-card" style="--c:#2f7d32">
                    <span class="ic"><i class="bi bi-tree"></i></span>
                    <div><h3>Responsible environment</h3><p>Environmental management and rehabilitation that safeguard communities and land.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($services): ?>
<section class="section">
    <div class="container">
        <div class="section-head center" data-aos="fade-up">
            <span class="eyebrow">What we do</span>
            <h2>Integrated services for the mining sector</h2>
            <p>From the first geological survey to the last rehabilitation, we support every stage with technical depth and practical training.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $i => $s): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
                    <article class="card-ym service-card">
                        <div class="ic"><i class="bi <?= e($s['icon']) ?>"></i></div>
                        <h3><a href="<?= url('services/' . $s['slug']) ?>"><?= e($s['title']) ?></a></h3>
                        <p><?= e($s['summary']) ?></p>
                        <span class="link-arrow">Learn more <i class="bi bi-arrow-right"></i></span>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section-soft">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="about-visual">
                    <img class="main" src="<?= asset('img/photo-4.jpg') ?>" alt="Miners attending a safety training session holding training booklets" loading="lazy">
                    <div class="badge-card"><strong>Training that happens on site</strong><small>Practical, role-based and in your team's language.</small></div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="eyebrow">About us</span>
                <h2 class="mt-3 mb-4" style="font-size:clamp(1.8rem,3.2vw,2.5rem)">Experience from the field, expertise from the classroom</h2>
                <p><?= e(setting('company_name')) ?> was established to support mining operators, cooperatives, government institutions and development partners in achieving safe, productive and environmentally sustainable mining operations.</p>
                <p>Our services are built on extensive practical experience within Rwanda's mining industry and delivered by qualified professionals with strong academic and field backgrounds in geology, mining operations, environmental management and responsible mineral sourcing.</p>
                <ul class="check-list">
                    <li><i class="bi bi-check-circle-fill"></i><span>Rwanda-registered, locally rooted team</span></li>
                    <li><i class="bi bi-check-circle-fill"></i><span>Consultancy and training under one roof</span></li>
                    <li><i class="bi bi-check-circle-fill"></i><span>Clear, practical recommendations your crews can act on</span></li>
                </ul>
                <a href="<?= url('about') ?>" class="btn btn-dark-ym mt-4">More about us</a>
            </div>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="section-head center" data-aos="fade-up">
            <span class="eyebrow">How we work</span>
            <h2>A simple process built around results</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($steps as $i => [$title, $text]): ?>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <div class="step"><div class="num">0<?= $i + 1 ?></div><h3><?= e($title) ?></h3><p><?= e($text) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($projects): ?>
<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div class="section-head mb-0" data-aos="fade-up">
                <span class="eyebrow">Our work</span>
                <h2>Featured projects</h2>
            </div>
            <a href="<?= url('projects') ?>" class="link-arrow">View all projects <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($projects as $i => $p): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <?php include __DIR__ . '/inc/project-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band" style="background-image:url('<?= asset('img/photo-3.jpg') ?>')">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h2>Ready to make your operation safer and more compliant?</h2>
                <p class="mb-0 text-white-50">Tell us about your site and we'll recommend the most practical next step.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= url('contact') ?>" class="btn btn-gold btn-lg">Talk to our team</a>
            </div>
        </div>
    </div>
</section>

<?php if ($posts): ?>
<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div class="section-head mb-0" data-aos="fade-up">
                <span class="eyebrow">News &amp; insights</span>
                <h2>Latest from Young Masters</h2>
            </div>
            <a href="<?= url('news') ?>" class="link-arrow">All articles <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($posts as $i => $post): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <?php include __DIR__ . '/inc/post-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => setting('company_name'),
    'url' => abs_url(),
    'logo' => abs_url('assets/img/logo.png'),
    'email' => setting('email'),
    'telephone' => setting('phone'),
    'address' => ['@type' => 'PostalAddress', 'streetAddress' => setting('address'), 'addressCountry' => 'RW'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

<?php require __DIR__ . '/inc/footer.php'; ?>
