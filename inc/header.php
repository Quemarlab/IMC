<?php
/**
 * Public layout — top. Pages set $page before including:
 *   $page = ['title' => '…', 'description' => '…', 'nav' => 'about', 'image' => '…', 'robots' => 'noindex'];
 */
require_once __DIR__ . '/../app/bootstrap.php';

$page = ($page ?? []) + ['title' => '', 'description' => '', 'nav' => '', 'image' => '', 'robots' => 'index,follow', 'type' => 'website'];
$company = setting('company_name');
$fullTitle = $page['title'] ? $page['title'] . ' | ' . $company : $company . ' — ' . setting('tagline');
$description = $page['description'] ?: setting('about_short');
$ogImage = $page['image'] ?: site_origin() . parse_url(asset('img/hero-training.jpg'), PHP_URL_PATH);
$canonical = abs_url(ltrim(substr(strtok($_SERVER['REQUEST_URI'] ?? '/', '#'), strlen(base_path())), '/'));

$nav = [
    'home' => ['', 'Home'],
    'about' => ['about', 'About'],
    'services' => ['services', 'Services'],
    'projects' => ['projects', 'Projects'],
    'news' => ['news', 'News'],
    'gallery' => ['gallery', 'Gallery'],
    'contact' => ['contact', 'Contact'],
];
$socials = ['linkedin' => 'bi-linkedin', 'facebook' => 'bi-facebook', 'twitter' => 'bi-twitter-x', 'instagram' => 'bi-instagram', 'youtube' => 'bi-youtube'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e(excerpt($description, 160)) ?>">
    <meta name="robots" content="<?= e($page['robots']) ?>">
    <meta name="theme-color" content="#1d2124">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <meta property="og:type" content="<?= e($page['type']) ?>">
    <meta property="og:site_name" content="<?= e($company) ?>">
    <meta property="og:title" content="<?= e($fullTitle) ?>">
    <meta property="og:description" content="<?= e(excerpt($description, 200)) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('img/logo-mark.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('vendor/aos/aos.css') ?>" rel="stylesheet">
    <link href="<?= asset('vendor/glightbox/glightbox.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/site.css') ?>" rel="stylesheet">
    <link rel="alternate" type="application/rss+xml" title="<?= e($company) ?> news" href="<?= url('feed.xml') ?>">
    <?= $page['head'] ?? '' ?>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar d-none d-md-block py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <a href="tel:<?= e(preg_replace('/[^+\d]/', '', setting('phone'))) ?>" class="me-4"><i class="bi bi-telephone"></i><?= e(setting('phone')) ?></a>
            <a href="mailto:<?= e(setting('email')) ?>" class="me-4"><i class="bi bi-envelope"></i><?= e(setting('email')) ?></a>
            <span><i class="bi bi-geo-alt"></i><?= e(setting('address')) ?></span>
        </div>
        <div class="social">
            <?php foreach ($socials as $key => $icon): if (setting($key)): ?>
                <a href="<?= e(setting($key)) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($key)) ?>"><i class="bi <?= $icon ?>"></i></a>
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>

<header class="site-nav navbar navbar-expand-lg sticky-top py-2" id="siteNav">
    <div class="container">
        <a class="navbar-brand" href="<?= url() ?>"><img src="<?= asset('img/logo.png') ?>" alt="<?= e($company) ?>" width="62" height="56"></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-1 text-dark"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php foreach ($nav as $key => [$path, $label]): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $page['nav'] === $key ? ' active' : '' ?>" href="<?= url($path) ?>"<?= $page['nav'] === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item"><a class="nav-link" href="<?= url('search') ?>" aria-label="Search"><i class="bi bi-search"></i><span class="d-lg-none ms-2">Search</span></a></li>
                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <a class="btn btn-gold btn-sm px-4 py-2" href="<?= url('contact') ?>">Request a consultation</a>
                </li>
            </ul>
        </div>
    </div>
</header>

<main id="main">
