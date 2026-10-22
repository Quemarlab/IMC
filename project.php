<?php
require_once __DIR__ . '/app/bootstrap.php';

use YM\Repositories\Projects;

$project = Projects::findPublicBySlug(input('slug'));
if (!$project) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$page = ['title' => $project['title'], 'description' => $project['summary'] ?: excerpt($project['body']), 'nav' => 'projects',
    'image' => $project['cover_image'] ? abs_url('ControlPanel/upload/projects/' . $project['cover_image']) : ''];
require __DIR__ . '/inc/header.php';

$related = array_filter(Projects::latest(4), fn($r) => (int) $r['id'] !== (int) $project['id']);
$hero = ['title' => $project['title'], 'lead' => $project['summary'], 'crumbs' => [['Projects', url('projects')], [$project['title'], null]]];
if ($project['cover_image']) {
    $hero['image'] = upload_url('projects', $project['cover_image']);
}
include __DIR__ . '/inc/page-hero.php';

$facts = array_filter([
    'Category' => $project['category'],
    'Client' => $project['client'],
    'Location' => $project['location'],
    'Completed' => $project['completed_on'] ? fmt_date($project['completed_on'], 'F Y') : '',
]);
?>
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <?php if ($project['cover_image']): ?>
                    <a href="<?= e(upload_url('projects', $project['cover_image'])) ?>" class="glightbox d-block mb-4">
                        <img class="img-fluid rounded-4 w-100" src="<?= e(upload_url('projects', $project['cover_image'])) ?>" alt="<?= e($project['title']) ?>">
                    </a>
                <?php endif; ?>
                <h2 class="h3 mb-3">Project overview</h2>
                <div class="prose"><?= $project['body'] /* sanitised on save */ ?></div>
            </div>
            <aside class="col-lg-4">
                <?php if ($facts): ?>
                <div class="sidebar-card">
                    <h3>Project details</h3>
                    <ul class="fact-list">
                        <?php foreach ($facts as $label => $value): ?><li><span><?= e($label) ?></span><strong><?= e($value) ?></strong></li><?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <div class="sidebar-card mt-4 bg-dark text-white">
                    <h3 class="text-white">Planning something similar?</h3>
                    <p class="small">We'd be glad to discuss your project.</p>
                    <a class="btn btn-gold w-100" href="<?= url('contact') ?>">Get in touch</a>
                </div>
            </aside>
        </div>

        <?php if ($related): ?>
            <h2 class="h3 mt-5 mb-4">More projects</h2>
            <div class="row g-4">
                <?php foreach (array_slice($related, 0, 3) as $p): ?>
                    <div class="col-md-6 col-lg-4"><?php include __DIR__ . '/inc/project-card.php'; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
