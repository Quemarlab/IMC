<?php /** @var array $post news row */ ?>
<article class="card-ym">
    <div class="thumb"><img src="<?= e(upload_url('news', $post['cover_image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy"></div>
    <div class="body">
        <div class="meta">
            <span class="tag"><?= e($post['category']) ?></span>
            <span><i class="bi bi-calendar3"></i><?= e(fmt_date($post['published_at'] ?: $post['created_at'])) ?></span>
            <span><i class="bi bi-clock"></i><?= reading_time($post['body']) ?> min read</span>
        </div>
        <h3><a href="<?= url('news/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3>
        <p><?= e($post['excerpt'] ?: excerpt($post['body'], 130)) ?></p>
        <span class="link-arrow">Read article <i class="bi bi-arrow-right"></i></span>
    </div>
</article>
