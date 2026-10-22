<?php
$page = ['title' => 'Gallery', 'description' => 'Photos from training sessions, site visits and projects by Young Masters LTD.', 'nav' => 'gallery'];
require __DIR__ . '/inc/header.php';

use YM\Repositories\GalleryItems;

$category = input('category');
$pg = GalleryItems::paginate([
    'public' => true, 'page' => (int) input('page', '1'), 'per' => 24,
    'where' => $category !== '' ? 'category = ?' : null, 'params' => $category !== '' ? [$category] : [],
]);
$categories = GalleryItems::categories();

$hero = ['title' => 'Gallery', 'lead' => 'Moments from training sessions, site visits and projects.', 'crumbs' => [['Gallery', null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <?php if ($categories): ?>
            <div class="filter-pills">
                <a href="<?= url('gallery') ?>" class="<?= $category === '' ? 'active' : '' ?>">All</a>
                <?php foreach ($categories as $c): ?>
                    <a href="?category=<?= rawurlencode($c) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= e($c) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$pg['items']): ?>
            <div class="empty-state"><i class="bi bi-images"></i>Photos will appear here soon.</div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($pg['items'] as $g): ?>
                    <a href="<?= e(upload_url('gallery', $g['image'])) ?>" class="glightbox" data-gallery="ym" data-title="<?= e($g['title']) ?>">
                        <img src="<?= e(upload_url('gallery', $g['image'])) ?>" alt="<?= e($g['title'] ?: 'Gallery photo') ?>" loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
            <?php include __DIR__ . '/inc/pagination.php'; ?>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
