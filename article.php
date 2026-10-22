<?php
require_once __DIR__ . '/app/bootstrap.php';

use YM\Repositories\Posts;

$article = Posts::findPublicBySlug(input('slug'));
if (!$article) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$page = ['title' => $article['title'], 'description' => $article['excerpt'] ?: excerpt($article['body']), 'nav' => 'news', 'type' => 'article',
    'image' => $article['cover_image'] ? abs_url('ControlPanel/upload/news/' . $article['cover_image']) : ''];
require __DIR__ . '/inc/header.php';

$related = Posts::related((int) $article['id'], $article['category'], 3);
$when = $article['published_at'] ?: $article['created_at'];
$shareUrl = rawurlencode(abs_url('news/' . $article['slug']));
$shareText = rawurlencode($article['title']);

$hero = ['title' => $article['title'], 'crumbs' => [['News', url('news')], [$article['title'], null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <article class="col-lg-9 col-xl-8">
                <div class="meta mb-4">
                    <span class="tag"><?= e($article['category']) ?></span>
                    <span><i class="bi bi-calendar3"></i><?= e(fmt_date($when, 'j F Y')) ?></span>
                    <span><i class="bi bi-clock"></i><?= reading_time($article['body']) ?> min read</span>
                </div>
                <?php if ($article['cover_image']): ?>
                    <img class="img-fluid rounded-4 mb-4 w-100" src="<?= e(upload_url('news', $article['cover_image'])) ?>" alt="<?= e($article['title']) ?>">
                <?php endif; ?>
                <div class="prose"><?= $article['body'] /* sanitised on save */ ?></div>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-5 pt-4 border-top">
                    <strong class="me-2 text-dark">Share:</strong>
                    <a class="btn btn-outline-dark btn-sm" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>"><i class="bi bi-linkedin"></i></a>
                    <a class="btn btn-outline-dark btn-sm" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareText ?>"><i class="bi bi-twitter-x"></i></a>
                    <a class="btn btn-outline-dark btn-sm" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>"><i class="bi bi-facebook"></i></a>
                    <a class="btn btn-outline-dark btn-sm" href="https://wa.me/?text=<?= $shareText ?>%20<?= $shareUrl ?>"><i class="bi bi-whatsapp"></i></a>
                </div>
            </article>
        </div>

        <?php if ($related): ?>
            <h2 class="h3 mt-5 mb-4">Keep reading</h2>
            <div class="row g-4">
                <?php foreach ($related as $post): ?>
                    <div class="col-md-6 col-lg-4"><?php include __DIR__ . '/inc/post-card.php'; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org', '@type' => 'NewsArticle',
    'headline' => $article['title'], 'datePublished' => date('c', strtotime($when)), 'dateModified' => date('c', strtotime($article['updated_at'])),
    'image' => $page['image'] ?: null,
    'publisher' => ['@type' => 'Organization', 'name' => setting('company_name')],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php require __DIR__ . '/inc/footer.php'; ?>
