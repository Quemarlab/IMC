<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\Backup;
use YM\Core\DB;

Auth::require('admin');

if (is_post()) {
    admin_post();
    @set_time_limit(120);
    $file = 'ym-backup-' . date('Ymd-His') . '.sql';
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Cache-Control: no-store');
    $out = fopen('php://output', 'w');
    Backup::dump($out, Backup::schemaTables());
    Audit::log('backup', 'database', null, $file);
    exit;
}

$tables = [];
foreach (Backup::schemaTables() as $t) {
    $tables[$t] = (int) DB::val("SELECT COUNT(*) FROM `$t`");
}
$legacy = Backup::legacyTables();

admin_header('Backup', 'backup');
?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card"><div class="card-header">Download a database backup</div>
            <div class="card-body">
                <p class="text-body-secondary">A plain <code>.sql</code> file with the structure and content of every site table (users, messages, projects, news …).
                    Restore it with phpMyAdmin or the <code>mysql</code> client. Uploaded images are not included — copy <code>ControlPanel/upload/</code> separately.</p>
                <form method="post"><?= csrf_field() ?><button class="btn btn-gold"><i class="bi bi-database-down me-1"></i>Download backup</button></form>
                <p class="small text-body-secondary mt-3 mb-0">Keep backups somewhere private: they contain password hashes and visitor emails.</p>
            </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-header">What is in the database</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($tables as $t => $n): ?>
                    <li class="list-group-item bg-transparent d-flex justify-content-between"><span><?= e($t) ?></span><span class="text-body-secondary"><?= $n ?> rows</span></li>
                <?php endforeach; ?>
            </ul>
            <?php if ($legacy): ?>
                <div class="card-body border-top small text-body-secondary"><i class="bi bi-exclamation-triangle text-warning me-1"></i>
                    <?= count($legacy) ?> unused legacy table(s) found (<?= e(implode(', ', $legacy)) ?>). Remove them from the server with <code>php database/cleanup-legacy.php</code>.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php admin_footer();
