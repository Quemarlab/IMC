<?php
/** @var array $hero ['title' => string, 'lead' => string, 'crumbs' => [[label, url|null], …], 'image' => string] */
$hero += ['lead' => '', 'crumbs' => [], 'image' => asset('img/photo-3.jpg')];
?>
<section class="page-hero" style="background-image:url('<?= e($hero['image']) ?>')">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <?php foreach ($hero['crumbs'] as [$label, $link]): ?>
                    <?php if ($link): ?>
                        <li class="breadcrumb-item"><a href="<?= e($link) ?>"><?= e($label) ?></a></li>
                    <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?= e(mb_strimwidth($label, 0, 60, '…')) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
        <h1><?= e($hero['title']) ?></h1>
        <?php if ($hero['lead']): ?><p class="lead"><?= e($hero['lead']) ?></p><?php endif; ?>
    </div>
</section>
