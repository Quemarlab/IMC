<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Auth;
use YM\Core\DB;
use YM\Core\Env;
use YM\Core\Mailer;
use YM\Repositories\GalleryItems;
use YM\Repositories\Messages;
use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Services;
use YM\Repositories\Subscribers;

$user = Auth::require();

$counts = Messages::counts();
$stats = [
    ['Unread messages', $counts['new'], 'bi-chat-dots', '#d4a017', admin_url('messages?status=new')],
    ['Projects', Projects::count(), 'bi-briefcase', '#2f7d32', admin_url('resource?r=projects')],
    ['News articles', Posts::count(), 'bi-newspaper', '#3b6ea5', admin_url('resource?r=news')],
    ['Subscribers', Subscribers::countActive(), 'bi-envelope-paper', '#8a5cb8', admin_url('subscribers')],
];
$daily = Messages::dailyCounts(30);
$max = max(1, max($daily));
$total30 = array_sum($daily);
$recent = DB::all('SELECT id, name, subject, body, status, created_at FROM messages ORDER BY id DESC LIMIT 6');
$drafts = DB::all("SELECT 'News' AS kind, title, updated_at, id, 'news' AS r FROM posts WHERE is_published = 0
                   UNION ALL SELECT 'Project', title, updated_at, id, 'projects' FROM projects WHERE is_published = 0
                   ORDER BY updated_at DESC LIMIT 5");

// System health: surfaces the misconfigurations that bite in production.
$uploadsOk = is_writable(YM_ROOT . '/ControlPanel/upload');
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$health = [
    [true, 'Database connected'],
    [$uploadsOk, $uploadsOk ? 'Upload folder writable' : 'Upload folder is not writable'],
    [Mailer::configured() ? true : null, Mailer::configured() ? 'Outgoing mail configured' : 'Mail not configured — set MAIL_USER / MAIL_PASS in .env'],
    [extension_loaded('gd') ? true : null, extension_loaded('gd') ? 'GD loaded: uploads are resized automatically' : 'PHP GD extension missing: uploads are stored as-is'],
    [Env::bool('APP_DEBUG') ? null : true, Env::bool('APP_DEBUG') ? 'Debug mode is ON — disable it in production' : 'Debug mode off'],
    [$https ? true : null, $https ? 'Served over HTTPS' : 'Not using HTTPS (fine locally, required in production)'],
];

admin_header('Dashboard', 'dashboard');
?>
<div class="mb-4">
    <h2 class="h4 mb-1">Hello, <?= e(explode(' ', $user['name'])[0]) ?> 👋</h2>
    <p class="text-body-secondary mb-0">Here's what's happening on the website.</p>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($stats as [$label, $value, $icon, $color, $href]): ?>
        <div class="col-6 col-xl-3">
            <a class="card stat-card" href="<?= e($href) ?>" style="--c:<?= $color ?>">
                <span class="ic"><i class="bi <?= $icon ?>"></i></span>
                <div><div class="num"><?= (int) $value ?></div><div class="lbl"><?= e($label) ?></div></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Enquiries — last 30 days</span><span class="badge text-bg-light"><?= $total30 ?> total</span>
            </div>
            <div class="card-body">
                <div class="chart-bars" role="img" aria-label="Messages received per day over the last 30 days">
                    <?php foreach ($daily as $date => $n): ?>
                        <div class="bar" style="height:<?= max(2, round($n / $max * 100)) ?>%" data-tip="<?= e(fmt_date($date, 'j M')) ?>: <?= $n ?>"></div>
                    <?php endforeach; ?>
                </div>
                <div class="chart-axis"><span><?= e(fmt_date(array_key_first($daily), 'j M')) ?></span><span>Today</span></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Latest messages</span><a class="btn btn-sm btn-light" href="<?= admin_url('messages') ?>">View all</a>
            </div>
            <?php if (!$recent): ?>
                <div class="empty-admin"><i class="bi bi-inbox"></i>No messages yet.</div>
            <?php else: ?>
                <div class="table-responsive"><table class="table table-hover">
                    <tbody>
                    <?php foreach ($recent as $m): ?>
                        <tr class="<?= $m['status'] === 'new' ? 'unread' : '' ?>">
                            <td><a class="row-title" href="<?= admin_url('messages?id=' . $m['id']) ?>"><?= e($m['name']) ?></a>
                                <div class="small text-body-secondary"><?= e(excerpt($m['subject'] ?: $m['body'], 70)) ?></div></td>
                            <td><?= status_badge($m['status']) ?></td>
                            <td class="text-end small text-body-secondary text-nowrap"><?= e(time_ago($m['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header">Quick actions</div>
            <div class="card-body d-grid gap-2">
                <a class="btn btn-gold" href="<?= admin_url('resource?r=news&action=new') ?>"><i class="bi bi-plus-lg me-1"></i>Write news article</a>
                <a class="btn btn-light" href="<?= admin_url('resource?r=projects&action=new') ?>"><i class="bi bi-briefcase me-1"></i>Add project</a>
                <a class="btn btn-light" href="<?= admin_url('resource?r=gallery&action=new') ?>"><i class="bi bi-image me-1"></i>Upload gallery photo</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">System health</div>
            <div class="card-body py-2"><ul class="list-unstyled health mb-0">
                <?php foreach ($health as [$ok, $text]): ?>
                    <li><i class="bi <?= $ok === true ? 'bi-check-circle-fill' : ($ok === null ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill') ?>"></i><span><?= e($text) ?></span></li>
                <?php endforeach; ?>
            </ul></div>
        </div>

        <?php if ($drafts): ?>
        <div class="card">
            <div class="card-header">Unpublished drafts</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($drafts as $d): ?>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                        <a class="row-title" href="<?= admin_url('resource?r=' . $d['r'] . '&action=edit&id=' . $d['id']) ?>"><?= e(mb_strimwidth($d['title'], 0, 34, '…')) ?></a>
                        <span class="badge text-bg-light"><?= e($d['kind']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php admin_footer();
