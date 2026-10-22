<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\Mailer;
use YM\Repositories\Subscribers;

Auth::require();

if (is_post()) {
    admin_post();
    $id = (int) input('id');
    $sub = $id ? Subscribers::find($id) : null;
    if ($sub && input('op') === 'delete') {
        Subscribers::delete($id);
        Audit::log('delete', 'subscriber', $id);
        flash('success', 'Subscriber removed.');
    } elseif ($sub && input('op') === 'resend' && $sub['status'] === 'pending') {
        $link = abs_url('confirm-subscription?token=' . $sub['token']);
        $sent = Mailer::send($sub['email'], 'Confirm your subscription',
            '<p>Please confirm your email address to start receiving news and project updates from ' . e(setting('company_name')) . '.</p>'
            . '<p style="margin:22px 0"><a href="' . e($link) . '" style="background:#d4a017;color:#1d2124;padding:11px 20px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block">Confirm my subscription</a></p>');
        flash($sent ? 'success' : 'error', $sent ? 'Confirmation email sent again.' : 'The email could not be sent. Check the mail settings in .env.');
    }
    redirect(admin_url('subscribers' . (input('status') ? '?status=' . rawurlencode(input('status')) : '')));
}

if (input('export') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="subscribers-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it correctly
    fputcsv($out, ['Email', 'Status', 'News', 'Projects', 'Subscribed', 'Confirmed', 'Unsubscribed']);
    foreach (Subscribers::all() as $s) {
        fputcsv($out, [ltrim($s['email'], '=+-@'), $s['status'], $s['want_news'] ? 'yes' : 'no', $s['want_projects'] ? 'yes' : 'no', $s['created_at'], $s['confirmed_at'], $s['unsubscribed_at']]); // neutralise CSV formula injection
    }
    Audit::log('export', 'subscriber', null, 'CSV export');
    exit;
}

$q = input('q');
$status = in_array(input('status'), Subscribers::STATUSES, true) ? input('status') : '';
$pg = Subscribers::paginate($q, (int) input('page', '1'), 20, $status);
$counts = Subscribers::counts();

admin_header('Subscribers', 'subscribers');
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <ul class="nav nav-pills">
        <?php foreach (['' => ['All', array_sum($counts)], 'active' => ['Active', $counts['active']], 'pending' => ['Waiting to confirm', $counts['pending']], 'unsubscribed' => ['Unsubscribed', $counts['unsubscribed']]] as $key => [$label, $n]): ?>
            <li class="nav-item"><a class="nav-link<?= $status === $key ? ' active' : '' ?>" href="<?= admin_url('subscribers' . ($key ? '?status=' . $key : '')) ?>"><?= $label ?> <span class="badge text-bg-light ms-1"><?= $n ?></span></a></li>
        <?php endforeach; ?>
    </ul>
    <div class="d-flex gap-2">
        <form method="get" class="d-flex gap-2"><?php if ($status): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Search email…" style="width:230px"></form>
        <a class="btn btn-light" href="<?= admin_url('subscribers?export=csv') ?>"><i class="bi bi-download me-1"></i>CSV</a>
        <a class="btn btn-gold" href="<?= admin_url('newsletter') ?>"><i class="bi bi-megaphone me-1"></i>Newsletter</a>
    </div>
</div>
<div class="card">
    <?php if (!$pg['items']): ?>
        <div class="empty-admin"><i class="bi bi-envelope-paper"></i>No subscribers here yet.</div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-hover">
            <thead><tr><th>Email</th><th>Status</th><th>Receives</th><th>Subscribed</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($pg['items'] as $s): ?>
                <tr>
                    <td><?= e($s['email']) ?></td>
                    <td><?= status_badge($s['status']) ?></td>
                    <td class="small text-body-secondary"><?= $s['status'] === 'active' ? implode(' · ', array_filter([$s['want_news'] ? 'News' : '', $s['want_projects'] ? 'Projects' : ''])) : '—' ?></td>
                    <td class="small text-body-secondary"><?= e(fmt_date($s['created_at'], 'j M Y')) ?></td>
                    <td class="text-end text-nowrap">
                        <?php if ($s['status'] === 'pending'): ?>
                            <form class="d-inline" method="post"><?= csrf_field() ?><input type="hidden" name="op" value="resend"><input type="hidden" name="id" value="<?= $s['id'] ?>"><input type="hidden" name="status" value="<?= e($status) ?>">
                                <button class="btn btn-sm btn-light" title="Send the confirmation email again"><i class="bi bi-envelope-arrow-up"></i></button></form>
                        <?php endif; ?>
                        <form class="d-inline" method="post" data-confirm="Remove this subscriber?"><?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>"><input type="hidden" name="status" value="<?= e($status) ?>">
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
