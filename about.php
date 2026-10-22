<?php
$page = [
    'title' => 'About us',
    'description' => 'Young Masters LTD is a Rwanda-registered mining consultancy and training company specializing in OHS, environmental management, mining compliance, geological services, mine planning and capacity building.',
    'nav' => 'about',
];
require __DIR__ . '/inc/header.php';

$hero = ['title' => 'About ' . setting('company_name'), 'lead' => 'A Rwanda-registered mining consultancy and training company.', 'crumbs' => [['About', null]]];
include __DIR__ . '/inc/page-hero.php';

$areas = [
    'Occupational Health & Safety (OHS)', 'Environmental Management', 'Mining Compliance',
    'Geological Services', 'Mine Planning', 'Capacity Building for the mining sector',
];
?>

<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="eyebrow">Who we are</span>
                <h2 class="mt-3 mb-4" style="font-size:clamp(1.8rem,3.2vw,2.5rem)">Safe, productive and sustainable mining — delivered by people who know the field</h2>
                <p><strong><?= e(setting('company_name')) ?></strong> is a Rwanda-registered mining consultancy and training company specializing in Occupational Health and Safety (OHS), Environmental Management, Mining Compliance, Geological Services, Mine Planning, and Capacity Building for the mining sector.</p>
                <p>The company was established to support mining operators, cooperatives, government institutions, and development partners in achieving safe, productive, and environmentally sustainable mining operations.</p>
                <p>Our services are built on extensive practical experience within Rwanda's mining industry and are delivered by highly qualified professionals with strong academic and field backgrounds in geology, mining operations, environmental management, and responsible mineral sourcing.</p>
                <div class="signature">
                    <img src="<?= asset('img/md-serge.jpg') ?>" alt="Serge NSENGIMANA, Managing Director" loading="lazy">
                    <div><strong>Serge NSENGIMANA</strong><span class="text-muted">Managing Director</span></div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img class="img-fluid rounded-4 shadow-lg" src="<?= asset('img/photo-3.jpg') ?>" alt="Mining team at a mine entrance after a site visit" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-head center" data-aos="fade-up">
            <span class="eyebrow">Our direction</span>
            <h2>Mission, vision &amp; approach</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="value-card"><i class="bi bi-bullseye ic"></i><h3 class="h5">Our mission</h3>
                    <p class="mb-0">To become a leading provider of mining consultancy, health and safety training, and environmental management services in Rwanda and the East African region.</p></div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="value-card"><i class="bi bi-eye ic"></i><h3 class="h5">Our vision</h3>
                    <p class="mb-0">To improve safety performance, environmental stewardship, and operational efficiency in the mining sector through professional training, technical support, and innovative solutions.</p></div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="value-card"><i class="bi bi-compass ic"></i><h3 class="h5">Our approach</h3>
                    <p class="mb-0">Practical, evidence-based and respectful of the people doing the work. We combine field experience with academic rigour and deliver advice teams can act on.</p></div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5" data-aos="fade-right">
                <span class="eyebrow">Focus areas</span>
                <h2 class="mt-3 mb-3" style="font-size:clamp(1.8rem,3vw,2.3rem)">What we specialise in</h2>
                <p>Six connected areas of practice let us support an operation end-to-end rather than in isolated pieces.</p>
                <a href="<?= url('services') ?>" class="btn btn-dark-ym mt-2">See all services</a>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="row g-3">
                    <?php foreach ($areas as $a): ?>
                        <div class="col-sm-6"><div class="value-card py-3 px-4 d-flex align-items-center gap-3"><i class="bi bi-check-circle-fill text-success fs-4"></i><strong class="text-dark"><?= e($a) ?></strong></div></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-band" style="background-image:url('<?= asset('img/hero-training.jpg') ?>')">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8"><h2>Let's talk about your operation</h2><p class="mb-0 text-white-50">Whether you run a mine, a cooperative or a programme, we'd be glad to help.</p></div>
            <div class="col-lg-4 text-lg-end"><a href="<?= url('contact') ?>" class="btn btn-gold btn-lg">Contact us</a></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
