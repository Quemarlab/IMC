<?php /** @var array $p project row */ ?>
<article class="card-ym">
    <div class="thumb"><img src="<?= e(upload_url('projects', $p['cover_image'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy"></div>
    <div class="body">
        <div class="meta">
            <?php if ($p['category']): ?><span class="tag"><?= e($p['category']) ?></span><?php endif; ?>
            <?php if ($p['location']): ?><span><i class="bi bi-geo-alt"></i><?= e($p['location']) ?></span><?php endif; ?>
        </div>
        <h3><a href="<?= url('projects/' . $p['slug']) ?>"><?= e($p['title']) ?></a></h3>
        <p><?= e($p['summary'] ?: excerpt($p['body'], 120)) ?></p>
        <span class="link-arrow">View project <i class="bi bi-arrow-right"></i></span>
    </div>
</article>
