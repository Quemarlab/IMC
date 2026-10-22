<?php
declare(strict_types=1);

/**
 * Shared admin layout. Pages call:
 *   $user = Auth::require();            // gate (optionally with roles)
 *   admin_header('Title', 'nav-key');   // …page markup…
 *   admin_footer();
 */
require_once __DIR__ . '/../../app/bootstrap.php';

use YM\Core\Auth;
use YM\Repositories\Messages;

function admin_asset(string $path): string
{
    $file = YM_ROOT . '/ControlPanel/static/' . $path;
    return admin_url('static/' . $path) . (is_file($file) ? '?v=' . filemtime($file) : '');
}

function admin_nav(): array
{
    $items = [
        ['Overview', [['dashboard', 'Dashboard', 'bi-speedometer2', null]]],
        ['Content', [
            ['projects', 'Projects', 'bi-briefcase', 'resource?r=projects'],
            ['news', 'News', 'bi-newspaper', 'resource?r=news'],
            ['services', 'Services', 'bi-gear', 'resource?r=services'],
            ['gallery', 'Gallery', 'bi-images', 'resource?r=gallery'],
        ]],
        ['Inbox', [
            ['messages', 'Messages', 'bi-chat-dots', null],
            ['subscribers', 'Subscribers', 'bi-envelope-paper', null],
        ]],
    ];
    if (Auth::is('admin')) {
        $items[] = ['Administration', [
            ['users', 'Users', 'bi-people', null],
            ['settings', 'Site settings', 'bi-sliders', null],
            ['activity', 'Activity log', 'bi-clock-history', null],
            ['backup', 'Backup', 'bi-database-down', null],
        ]];
    }
    return $items;
}

function admin_header(string $title, string $active = '', string $head = ''): void
{
    $user = Auth::user();
    $unread = Messages::unread();
    $initials = strtoupper(mb_substr($user['name'], 0, 1));
    ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title) ?> · <?= e(setting('company_name')) ?> admin</title>
    <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= admin_asset('admin.css') ?>" rel="stylesheet">
    <?= $head ?>
    <script src="<?= admin_asset('theme.js') ?>"></script>
</head>
<body class="admin" data-poll-url="<?= e(admin_url('messages?poll=1')) ?>" data-latest-msg="<?= Messages::latestId() ?>" data-unread="<?= $unread ?>" data-base-title="<?= e($title) ?>">
<div class="admin-shell">
    <aside class="admin-sidebar" id="sidebar">
        <a class="brand" href="<?= admin_url('dashboard') ?>">
            <span class="brand-chip"><img src="<?= asset('img/logo-mark.png') ?>" alt="" width="40" height="40"></span>
            <span><strong>Young Masters</strong><small>Admin panel</small></span>
        </a>
        <nav>
            <?php foreach (admin_nav() as [$group, $links]): ?>
                <div class="nav-group"><?= e($group) ?></div>
                <?php foreach ($links as [$key, $label, $icon, $href]): ?>
                    <a class="nav-item<?= $active === $key ? ' active' : '' ?>" href="<?= admin_url($href ?? $key) ?>">
                        <i class="bi <?= $icon ?>"></i><span><?= e($label) ?></span>
                        <?php if ($key === 'messages' && $unread): ?><span class="badge-count"><?= $unread ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <a class="nav-item view-site" href="<?= url() ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i><span>View website</span></a>
    </aside>
    <div class="admin-backdrop" id="backdrop"></div>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="btn btn-icon d-lg-none" id="sidebarToggle" type="button" aria-label="Menu"><i class="bi bi-list fs-4"></i></button>
            <h1><?= e($title) ?></h1>
            <div class="ms-auto d-flex align-items-center gap-2">
                <button class="btn btn-icon d-none" id="notifyToggle" type="button" title="Get desktop alerts for new messages" aria-label="Enable desktop notifications"><i class="bi bi-bell"></i></button>
                <button class="btn btn-icon" id="themeToggle" type="button" aria-label="Toggle dark mode"><i class="bi bi-moon-stars"></i></button>
                <div class="dropdown">
                    <button class="user-chip dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar"><?= e($initials) ?></span>
                        <span class="d-none d-md-block text-start"><strong><?= e($user['name']) ?></strong><small><?= e(ucfirst($user['role'])) ?></small></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="<?= admin_url('profile') ?>"><i class="bi bi-person me-2"></i>My profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= admin_url('logout') ?>"><?= csrf_field() ?>
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        <main class="admin-content">
            <?php foreach (flash() as $f): ?>
                <div class="alert alert-<?= $f['type'] === 'error' ? 'danger' : e($f['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($f['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
    <?php
}

function admin_footer(array $scripts = []): void
{
    ?>
        </main>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-body p-4 text-center">
            <i class="bi bi-exclamation-triangle text-danger display-5"></i>
            <h2 class="h5 mt-3" id="confirmTitle">Are you sure?</h2>
            <p class="text-body-secondary mb-4" id="confirmText">This cannot be undone.</p>
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmOk">Yes, continue</button>
        </div>
    </div></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastWrap" style="z-index:1090"></div>

<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<?php foreach ($scripts as $s): ?><script src="<?= e($s) ?>"></script><?php endforeach; ?>
<script src="<?= admin_asset('admin.js') ?>"></script>
</body>
</html>
    <?php
}

/** Reads the current POST's CSRF token or aborts. Use at the top of every state-changing handler. */
function admin_post(): void
{
    if (!is_post()) {
        http_response_code(405);
        exit('Method not allowed');
    }
    \YM\Core\Csrf::check();
}

function status_badge(string $status): string
{
    $map = ['new' => 'primary', 'read' => 'secondary', 'replied' => 'success', 'archived' => 'dark',
        'active' => 'success', 'disabled' => 'danger', 'unsubscribed' => 'secondary',
        'published' => 'success', 'draft' => 'warning', 'scheduled' => 'info'];
    return '<span class="badge text-bg-' . ($map[$status] ?? 'secondary') . '">' . e(ucfirst($status)) . '</span>';
}
