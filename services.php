<?php
$page = [
    'title' => 'Services',
    'description' => 'Occupational health & safety, environmental management, mining compliance, geological services, mine planning and capacity building for the mining sector in Rwanda.',
    'nav' => 'services',
];
require __DIR__ . '/inc/header.php';

use YM\Repositories\Services;

$services = Services::latest(50);
$hero = ['title' => 'Our services', 'lead' => 'Consultancy and training across the full mining lifecycle.', 'crumbs' => [['Services', null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <?php if (!$services): ?>
            <div class="empty-state"><i class="bi bi-gear"></i>Services will be listed here soon.</div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
