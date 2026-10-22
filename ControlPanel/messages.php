<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\Mailer;
use YM\Repositories\Messages;

Auth::require();

$id = (int) input('id');

if (is_post()) {
    admin_post();
    $op = input('op');
    $msg = $id ? Messages::find($id) : null;
    if (!$msg) {
        flash('error', 'Message not found.');
        redirect(admin_url('messages'));
    }

    switch ($op) {
        case 'status':
            Messages::setStatus($id, input('status'));
            flash('success', 'Message marked as ' . input('status') . '.');
            redirect(admin_url('messages?id=' . $id));
        case 'delete':
            Messages::delete($id);
            Audit::log('delete', 'message', $id, $msg['email']);
            flash('success', 'Message deleted.');
            redirect(admin_url('messages'));
        case 'reply':
            $reply = trim($_POST['reply'] ?? '');
            if (mb_strlen($reply) < 2) {
                flash('error', 'Write a reply first.');
                redirect(admin_url('messages?id=' . $id));
            }
            $quoted = '<blockquote style="border-left:3px solid #d4a017;margin:18px 0;padding:6px 14px;color:#777">' . nl2br(e($msg['body'])) . '</blockquote>';
            $sent = Mailer::send(
                $msg['email'],
                'Re: ' . ($msg['subject'] ?: 'Your enquiry'),
                '<p>Dear ' . e($msg['name']) . ',</p><p style="white-space:pre-wrap">' . e($reply) . '</p><p>Kind regards,<br>' . e(Auth::user()['name']) . '<br>' . e(setting('company_name')) . '</p>'
                . '<p style="color:#999;font-size:12px">In reply to your message:</p>' . $quoted,
                Auth::user()["email"],
                $msg['name']
            );
            if ($sent) {
                Messages::setStatus($id, 'replied');
                Audit::log('reply', 'message', $id, $msg['email']);
                flash('success', 'Reply sent to ' . $msg['email'] . '.');
            } else {
                flash('error', 'The email could not be sent. Check the mail settings in .env, or reply from your mail client.');
            }
            redirect(admin_url('messages?id=' . $id));
    }
    redirect(admin_url('messages'));
}

/** Replies go out from the shared mailbox but staff replies should reach the mailbox owner. */
function Env_replyTo(): ?string
{
    return \YM\Core\Env::get('MAIL_TO');
}

// ------------------------------------------------------------ detail view
if ($id) {
    $m = Messages::find($id);
    if (!$m) {
        flash('error', 'Message not found.');
        redirect(admin_url('messages'));
    }
    if ($m['status'] === 'new') {
        Messages::setStatus($id, 'read');
        $m['status'] = 'read';
    }
    admin_header('Message', 'messages');
    ?>
    <a class="btn btn-sm btn-light mb-3" href="<?= admin_url('messages') ?>"><i class="bi bi-arrow-left me-1"></i>All messages</a>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card"><div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h5 mb-1"><?= e($m['subject'] ?: 'Website enquiry') ?></h2>
                        <div class="text-body-secondary small">From <strong><?= e($m['name']) ?></strong> · <?= e(fmt_date($m['created_at'], 'j M Y, H:i')) ?> · <?= e(time_ago($m['created_at'])) ?></div>
                    </div>
                    <?= status_badge($m['status']) ?>
                </div>
                <div class="message-body"><?= e($m['body']) ?></div>
            </div></div>

            <div class="card mt-4">
                <div class="card-header">Reply by email</div>
                <div class="card-body">
                    <form method="post" action="<?= admin_url('messages?id=' . $id) ?>">
                        <?= csrf_field() ?><input type="hidden" name="op" value="reply">
                        <textarea class="form-control mb-3" name="reply" rows="6" placeholder="Write your reply to <?= e($m['name']) ?>…" required></textarea>
                        <button class="btn btn-gold"<?= Mailer::configured() ? '' : ' disabled' ?>><i class="bi bi-send me-1"></i>Send reply</button>
                        <a class="btn btn-light ms-1" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . ($m['subject'] ?: 'Your enquiry')) ?>">Open in mail app</a>
                        <?php if (!Mailer::configured()): ?><span class="small text-danger ms-2">Mail is not configured.</span><?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4"><div class="card-header">Sender</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent"><i class="bi bi-envelope me-2 text-body-secondary"></i><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></li>
                    <?php if ($m['phone']): ?><li class="list-group-item bg-transparent"><i class="bi bi-telephone me-2 text-body-secondary"></i><a href="tel:<?= e($m['phone']) ?>"><?= e($m['phone']) ?></a></li><?php endif; ?>
                    <li class="list-group-item bg-transparent small text-body-secondary"><i class="bi bi-hdd-network me-2"></i>IP <?= e($m['ip'] ?: 'unknown') ?></li>
                </ul></div>
            <div class="card"><div class="card-header">Actions</div><div class="card-body d-grid gap-2">
                <?php foreach (['new' => 'Mark as unread', 'replied' => 'Mark as replied', 'archived' => 'Archive'] as $st => $label): if ($m['status'] === $st) continue; ?>
                    <form method="post" action="<?= admin_url('messages?id=' . $id) ?>"><?= csrf_field() ?><input type="hidden" name="op" value="status"><input type="hidden" name="status" value="<?= $st ?>">
                        <button class="btn btn-light w-100 text-start"><?= e($label) ?></button></form>
                <?php endforeach; ?>
                <form method="post" action="<?= admin_url('messages?id=' . $id) ?>" data-confirm="Delete this message?"><?= csrf_field() ?><input type="hidden" name="op" value="delete">
                    <button class="btn btn-outline-danger w-100 text-start"><i class="bi bi-trash me-1"></i>Delete</button></form>
            </div></div>
        </div>
    </div>
    <?php
    admin_footer();
    exit;
}

// ------------------------------------------------------------ CSV export
if (input('export') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="messages-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "﻿");
    fputcsv($out, ['Received', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status']);
    foreach (\YM\Core\DB::all('SELECT * FROM messages ORDER BY id DESC') as $r) {
        fputcsv($out, array_map(fn($v) => preg_match('/^[=+\-@	]/', (string) $v) ? "'" . $v : $v,
            [$r['created_at'], $r['name'], $r['email'], $r['phone'], $r['subject'], $r['body'], $r['status']]));
    }
    Audit::log('export', 'message', null, 'CSV export');
    exit;
}

// ------------------------------------------------------------ list view
$status = input('status');
$q = input('q');
$pg = Messages::paginate($status, $q, (int) input('page', '1'));
$counts = Messages::counts();

admin_header('Messages', 'messages');
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <ul class="nav nav-pills">
        <?php foreach (['' => ['Inbox', $counts['new'] + $counts['read'] + $counts['replied']], 'new' => ['Unread', $counts['new']], 'replied' => ['Replied', $counts['replied']], 'archived' => ['Archived', $counts['archived']]] as $key => [$label, $n]): ?>
            <li class="nav-item"><a class="nav-link<?= $status === $key ? ' active' : '' ?>" href="<?= admin_url('messages' . ($key ? '?status=' . $key : '')) ?>"><?= $label ?> <span class="badge text-bg-light ms-1"><?= $n ?></span></a></li>
        <?php endforeach; ?>
    </ul>
    <form method="get" class="d-flex gap-2"><?php if ($status): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
        <input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Search messages…" style="width:230px">
        <a class="btn btn-light" href="<?= admin_url('messages?export=csv') ?>" title="Export all messages as CSV"><i class="bi bi-download"></i></a></form>
</div>
<div class="card">
    <?php if (!$pg['items']): ?>
        <div class="empty-admin"><i class="bi bi-inbox"></i>No messages here.</div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-hover">
            <thead><tr><th>Sender</th><th>Message</th><th>Status</th><th>Received</th></tr></thead>
            <tbody>
            <?php foreach ($pg['items'] as $m): ?>
                <tr class="<?= $m['status'] === 'new' ? 'unread' : '' ?>">
                    <td><a class="row-title" href="<?= admin_url('messages?id=' . $m['id']) ?>"><?= e($m['name']) ?></a><div class="small text-body-secondary"><?= e($m['email']) ?></div></td>
                    <td class="text-body-secondary" style="max-width:380px"><strong class="text-body"><?= e($m['subject']) ?></strong> <?= e(excerpt($m['body'], 90)) ?></td>
                    <td><?= status_badge($m['status']) ?></td>
                    <td class="small text-body-secondary text-nowrap"><?= e(time_ago($m['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <div class="card-body py-2"><?php $pgClass = 'mb-0'; include __DIR__ . '/../inc/pagination.php'; ?></div>
    <?php endif; ?>
</div>
<?php admin_footer();
