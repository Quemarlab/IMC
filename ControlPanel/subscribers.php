<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Repositories\Subscribers;

Auth::require();

if (is_post()) {
    admin_post();
    if (input('op') === 'delete' && ($id = (int) input('id'))) {
        Subscribers::delete($id);
        Audit::log('delete', 'subscriber', $id);
        flash('success', 'Subscriber removed.');
    }
    redirect(admin_url('subscribers'));
}

if (input('export') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="subscribers-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it correctly
    fputcsv($out, ['Email', 'Status', 'Subscribed']);
    foreach (Subscribers::all() as $s) {
        fputcsv($out, [ltrim($s['email'], '=+-@'), $s['status'], $s['created_at']]); // neutralise CSV formula injection
    }
    Audit::log('export', 'subscriber', null, 'CSV export');
    exit;
}

$q = input('q');
$pg = Subscribers::paginate($q, (int) input('page', '1'));

admin_header('Subscribers', 'subscribers');
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <form method="get" class="d-flex gap-2"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Search email…" style="width:260px"></form>
    <a class="btn btn-light" href="<?= admin_url('subscribers?export=csv') ?>"><i class="bi bi-download me-1"></i>Export CSV</a>
</div>
<div class="card">
    <?php if (!$pg['items']): ?>
        <div class="empty-admin"><i class="bi bi-envelope-paper"></i>No subscribers yet.</div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-hover">
            <thead><tr><th>Email</th><th>Status</th><th>Subscribed</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($pg['items'] as $s): ?>
                <tr>
                    <td><?= e($s['email']) ?></td><td><?= status_badge($s['status']) ?></td>
                    <td class="small text-body-secondary"><?= e(fmt_date($s['created_at'], 'j M Y')) ?></td>
                    <td class="text-end">
                        <form method="post" data-confirm="Remove this subscriber?"><?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <div class="card-body py-2 small text-body-secondary d-flex justify-content-between align-items-center"><span><?= $pg['total'] ?> subscribers</span>
            <?php $pgClass = 'mb-0'; include __DIR__ . '/../inc/pagination.php'; ?></div>
    <?php endif; ?>
</div>
<?php admin_footer();
