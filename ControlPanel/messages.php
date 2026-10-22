<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\Csrf;
use YM\Core\DB;
use YM\Core\Mailer;
use YM\Repositories\Messages;

/** One inbox row; also rendered server-side for rows pushed by the live poll. */
function msg_row(array $m, int $activeId = 0): string
{
    return '<a class="msg-row msg-open' . ($m['status'] === 'new' ? ' unread' : '') . ($activeId === (int) $m['id'] ? ' active' : '') . '" data-id="' . (int) $m['id']
        . '" data-status="' . e($m['status']) . '" href="' . e(admin_url('messages?id=' . $m['id'])) . '">'
        . '<div class="d-flex justify-content-between gap-2"><span class="msg-from">' . e($m['name']) . '</span><span class="msg-time">' . e(time_ago($m['created_at'])) . '</span></div>'
        . '<div class="msg-subject">' . e($m['subject'] ?: 'Website enquiry') . '</div>'
        . '<div class="msg-excerpt">' . e(excerpt($m['body'], 90)) . '</div>'
        . '<div class="msg-status">' . ($m['status'] === 'new' ? '' : status_badge($m['status'])) . '</div></a>';
}

// ------------------------------------------------------------ live poll (read-only, does not extend the session)
if (input('poll') !== '') {
    if (!Auth::peek()) {
        json_response(['ok' => false], 401);
    }
    session_write_close();
    $items = [];
    foreach (Messages::since((int) input('after')) as $m) {
        $items[] = ['id' => (int) $m['id'], 'name' => $m['name'], 'subject' => $m['subject'] ?: 'Website enquiry',
            'excerpt' => excerpt($m['body'], 90), 'html' => msg_row($m)];
    }
    json_response(['ok' => true, 'unread' => Messages::unread(), 'latest' => Messages::latestId(), 'items' => $items]);
}

Auth::require();

$id = (int) input('id');
$ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

/** Finish a POST: JSON for the inbox pane, flash + redirect for plain form posts. */
function msg_done(bool $ok, string $message, string $to, array $extra = []): never
{
    global $ajax;
    if ($ajax) {
        json_response(['ok' => $ok, 'message' => $message, 'unread' => Messages::unread()] + $extra, $ok ? 200 : 422);
    }
    flash($ok ? 'success' : 'error', $message);
    redirect($to);
}

if (is_post()) {
    if ($ajax && !Csrf::valid()) {
        json_response(['ok' => false, 'message' => 'Your session expired. Refresh the page and try again.'], 403);
    }
    admin_post();
    $op = input('op');
    $msg = $id ? Messages::find($id) : null;
    if (!$msg) {
        msg_done(false, 'Message not found.', admin_url('messages'));
    }
    $back = admin_url('messages?id=' . $id);

    switch ($op) {
        case 'status':
            Messages::setStatus($id, input('status'));
            msg_done(true, 'Message marked as ' . input('status') . '.', $back);
        case 'delete':
            Messages::delete($id);
            Audit::log('delete', 'message', $id, $msg['email']);
            msg_done(true, 'Message deleted.', admin_url('messages'), ['deleted' => true]);
        case 'reply':
            $reply = trim($_POST['reply'] ?? '');
            if (mb_strlen($reply) < 2) {
                msg_done(false, 'Write a reply first.', $back);
            }
            $quoted = '<blockquote style="border-left:3px solid #d4a017;margin:18px 0;padding:6px 14px;color:#777">' . nl2br(e($msg['body'])) . '</blockquote>';
            $sent = Mailer::send(
                $msg['email'],
                'Re: ' . ($msg['subject'] ?: 'Your enquiry'),
                '<p>Dear ' . e($msg['name']) . ',</p><p style="white-space:pre-wrap">' . e($reply) . '</p><p>Kind regards,<br>' . e(Auth::user()['name']) . '<br>' . e(setting('company_name')) . '</p>'
                . '<p style="color:#999;font-size:12px">In reply to your message:</p>' . $quoted,
                Auth::user()['email'],
                $msg['name']
            );
            if (!$sent) {
                msg_done(false, 'The email could not be sent. Check the mail settings in .env, or reply from your mail client.', $back);
            }
            Messages::setStatus($id, 'replied');
            Audit::log('reply', 'message', $id, $msg['email']);
            msg_done(true, 'Reply sent to ' . $msg['email'] . '.', $back);
    }
    msg_done(false, 'Unknown action.', admin_url('messages'));
}

// ------------------------------------------------------------ detail (full page or inbox pane)
if ($id) {
    $m = Messages::find($id);
    if (!$m) {
        if (input('partial') !== '') {
            json_response(['ok' => false, 'message' => 'Message not found.'], 404);
        }
        flash('error', 'Message not found.');
        redirect(admin_url('messages'));
    }
    if ($m['status'] === 'new') {
        Messages::setStatus($id, 'read');
        $m['status'] = 'read';
    }
    if (input('partial') !== '') {
        header('X-Unread: ' . Messages::unread());
        header('Cache-Control: no-store');
        include __DIR__ . '/inc/message-detail.php';
        exit;
    }
    admin_header('Message', 'messages');
    ?>
    <a class="btn btn-sm btn-light mb-3" href="<?= admin_url('messages') ?>"><i class="bi bi-arrow-left me-1"></i>All messages</a>
    <div class="row"><div class="col-lg-9"><?php include __DIR__ . '/inc/message-detail.php'; ?></div></div>
    <?php
    admin_footer([admin_asset('inbox.js')]);
    exit;
}

// ------------------------------------------------------------ CSV export
if (input('export') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="messages-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Received', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status']);
    foreach (DB::all('SELECT * FROM messages ORDER BY id DESC') as $r) {
        fputcsv($out, array_map(fn($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : $v,
            [$r['created_at'], $r['name'], $r['email'], $r['phone'], $r['subject'], $r['body'], $r['status']]));
    }
    Audit::log('export', 'message', null, 'CSV export');
    exit;
}

// ------------------------------------------------------------ list view (two-pane inbox)
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

<div class="row g-3 inbox" data-live="<?= ($status === '' || $status === 'new') && $q === '' && $pg['page'] === 1 ? '1' : '' ?>">
    <div class="col-lg-5 col-xl-4">
        <div class="card inbox-list-card">
            <div id="msgList">
                <?php foreach ($pg['items'] as $m) echo msg_row($m); ?>
            </div>
            <div class="empty-admin<?= $pg['items'] ? ' d-none' : '' ?>" id="msgEmpty"><i class="bi bi-inbox"></i>No messages here.</div>
            <?php if ($pg['pages'] > 1): ?><div class="card-body py-2"><?php $pgClass = 'mb-0 pagination-sm'; include __DIR__ . '/../inc/pagination.php'; ?></div><?php endif; ?>
        </div>
    </div>
    <div class="col-lg-7 col-xl-8 d-none d-lg-block">
        <div id="msgPane" class="inbox-pane">
            <div class="empty-admin"><i class="bi bi-envelope-open"></i>Select a message to read it here.</div>
        </div>
    </div>
</div>
<?php admin_footer([admin_asset('inbox.js')]);
