<?php
require_once __DIR__ . '/app/bootstrap.php';

use YM\Core\RateLimiter;
use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Services;

$q = mb_substr(trim(input('q')), 0, 80);
$results = ['Services' => [], 'Projects' => [], 'News' => []];
if (mb_strlen($q) >= 2 && RateLimiter::hit('search:' . client_ip(), 60, 60)) {
    $opt = ['q' => $q, 'public' => true, 'per' => 6];
    $results['Services'] = Services::paginate($opt)['items'];
    $results['Projects'] = Projects::paginate($opt)['items'];
    $results['News'] = Posts::paginate($opt)['items'];
}
$total = array_sum(array_map('count', $results));

$page = ['title' => $q ? 'Search: ' . $q : 'Search', 'description' => 'Search services, projects and news.', 'robots' => 'noindex,follow'];
require __DIR__ . '/inc/header.php';

$hero = ['title' => 'Search', 'lead' => $q ? ($total . ' result' . ($total === 1 ? '' : 's') . ' for “' . $q . '”') : 'Find services, projects and news.', 'crumbs' => [['Search', null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <form class="row justify-content-center mb-5" action="<?= url('search') ?>" method="get" role="search">
            <div class="col-md-8 col-lg-6"><div class="input-group input-group-lg">
                <input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Search the website…" aria-label="Search" maxlength="80" autofocus>
                <button class="btn btn-gold" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
            </div></div>
        </form>

        <?php if ($q !== '' && !$total): ?>
            <div class="empty-state"><i class="bi bi-search"></i>Nothing matched “<?= e($q) ?>”. Try a different word, or <a href="<?= url('contact') ?>">ask us directly</a>.</div>
        <?php endif; ?>

        <?php if ($results['Services']): ?>
            <h2 class="h4 mb-3">Services</h2>
            <div class="row g-4 mb-5">
                <?php foreach ($results['Services'] as $s): ?>
                    <div class="col-md-6 col-lg-4"><article class="card-ym service-card">
                        <div class="ic"><i class="bi <?= e($s['icon']) ?>"></i></div>
                        <h3><a href="<?= url('services/' . $s['slug']) ?>"><?= e($s['title']) ?></a></h3>
                        <p><?= e($s['summary']) ?></p>
                    </article></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($results['Projects']): ?>
            <h2 class="h4 mb-3">Projects</h2>
            <div class="row g-4 mb-5">
                <?php foreach ($results['Projects'] as $p): ?><div class="col-md-6 col-lg-4"><?php include __DIR__ . '/inc/project-card.php'; ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($results['News']): ?>
            <h2 class="h4 mb-3">News</h2>
            <div class="row g-4">
                <?php foreach ($results['News'] as $post): ?><div class="col-md-6 col-lg-4"><?php include __DIR__ . '/inc/post-card.php'; ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
