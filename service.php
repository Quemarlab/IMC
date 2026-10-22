<?php
require_once __DIR__ . '/app/bootstrap.php';

use YM\Repositories\Services;

$service = Services::findPublicBySlug(input('slug'));
if (!$service) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$page = ['title' => $service['title'], 'description' => $service['summary'], 'nav' => 'services',
    'image' => $service['cover_image'] ? abs_url('ControlPanel/upload/services/' . $service['cover_image']) : ''];
require __DIR__ . '/inc/header.php';

$others = array_filter(Services::latest(12), fn($s) => (int) $s['id'] !== (int) $service['id']);
$hero = ['title' => $service['title'], 'lead' => $service['summary'], 'crumbs' => [['Services', url('services')], [$service['title'], null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <?php if ($service['cover_image']): ?>
                    <img class="img-fluid rounded-4 mb-4 w-100" src="<?= e(upload_url('services', $service['cover_image'])) ?>" alt="<?= e($service['title']) ?>">
                <?php endif; ?>
                <div class="prose"><?= $service['body'] /* sanitised on save */ ?></div>
                <a href="<?= url('contact') ?>?subject=<?= rawurlencode($service['title']) ?>" class="btn btn-gold btn-lg mt-4">Enquire about this service</a>
            </div>
            <aside class="col-lg-4">
                <div class="sidebar-card">
                    <h3>Other services</h3>
                    <ul class="list-unstyled mb-0 d-grid gap-2">
                        <?php foreach ($others as $o): ?>
                            <li><a class="d-flex gap-2 align-items-center text-dark fw-semibold" href="<?= url('services/' . $o['slug']) ?>"><i class="bi <?= e($o['icon']) ?> text-warning"></i><?= e($o['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sidebar-card mt-4 bg-dark text-white">
                    <h3 class="text-white">Need advice?</h3>
                    <p class="small">Talk to a specialist about your site.</p>
                    <a class="btn btn-gold w-100" href="tel:<?= e(preg_replace('/[^+\d]/', '', setting('phone'))) ?>"><i class="bi bi-telephone me-2"></i><?= e(setting('phone')) ?></a>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
