<?php
$page = ['title' => 'Projects', 'description' => 'Selected mining consultancy, safety, environmental and training projects delivered by Young Masters LTD.', 'nav' => 'projects'];
require __DIR__ . '/inc/header.php';

use YM\Repositories\Projects;

$category = input('category');
$q = input('q');
$pg = Projects::paginate([
    'public' => true, 'page' => (int) input('page', '1'), 'per' => 9, 'q' => $q,
    'where' => $category !== '' ? 'category = ?' : null, 'params' => $category !== '' ? [$category] : [],
]);
$categories = Projects::categories();

$hero = ['title' => 'Our projects', 'lead' => 'Practical results delivered across Rwanda\'s mining sector.', 'crumbs' => [['Projects', null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <div class="row g-3 align-items-start mb-4">
            <div class="col-lg-8">
                <?php if ($categories): ?>
                    <div class="filter-pills mb-0">
                        <a href="<?= url('projects') ?>" class="<?= $category === '' ? 'active' : '' ?>">All</a>
                        <?php foreach ($categories as $c): ?>
                            <a href="?category=<?= rawurlencode($c) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= e($c) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <form method="get" role="search"><div class="input-group">
                    <input type="search" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Search projects…" aria-label="Search projects">
                    <button class="btn btn-dark-ym" aria-label="Search"><i class="bi bi-search"></i></button>
                </div></form>
            </div>
        </div>

        <?php if (!$pg['items']): ?>
            <div class="empty-state"><i class="bi bi-folder2-open"></i><?= ($q || $category) ? 'No projects match your search.' : 'Project case studies are coming soon.' ?></div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pg['items'] as $i => $p): ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>"><?php include __DIR__ . '/inc/project-card.php'; ?></div>
                <?php endforeach; ?>
            </div>
            <?php include __DIR__ . '/inc/pagination.php'; ?>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
