<?php
$page = ['title' => 'News & insights', 'description' => 'News, updates and insights on mining safety, environment and compliance from Young Masters LTD.', 'nav' => 'news'];
require __DIR__ . '/inc/header.php';

use YM\Repositories\Posts;

$category = input('category');
$q = input('q');
$pg = Posts::paginate([
    'public' => true, 'page' => (int) input('page', '1'), 'per' => 9, 'q' => $q,
    'where' => $category !== '' ? 'category = ?' : null, 'params' => $category !== '' ? [$category] : [],
]);
$categories = Posts::categories();

$hero = ['title' => 'News & insights', 'lead' => 'Updates from the field and practical guidance for the mining sector.', 'crumbs' => [['News', null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <div class="row g-3 align-items-start mb-4">
            <div class="col-lg-8">
                <?php if ($categories): ?>
                    <div class="filter-pills mb-0">
                        <a href="<?= url('news') ?>" class="<?= $category === '' ? 'active' : '' ?>">All</a>
                        <?php foreach ($categories as $c): ?>
                            <a href="?category=<?= rawurlencode($c) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= e($c) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <form method="get" role="search"><div class="input-group">
                    <input type="search" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search articles…" aria-label="Search articles">
                    <button class="btn btn-dark-ym" aria-label="Search"><i class="bi bi-search"></i></button>
                </div></form>
            </div>
        </div>

        <?php if (!$pg['items']): ?>
            <div class="empty-state"><i class="bi bi-newspaper"></i><?= ($q || $category) ? 'No articles match your search.' : 'No news published yet — check back soon.' ?></div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pg['items'] as $i => $post): ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>"><?php include __DIR__ . '/inc/post-card.php'; ?></div>
                <?php endforeach; ?>
            </div>
            <?php include __DIR__ . '/inc/pagination.php'; ?>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
