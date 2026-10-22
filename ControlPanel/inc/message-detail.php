<?php
/** @var array $m message row; shared by the full page and the inbox side pane. */
use YM\Core\Mailer;
$action = admin_url('messages?id=' . $m['id']);
?>
<div class="msg-detail" data-id="<?= (int) $m['id'] ?>" data-status="<?= e($m['status']) ?>">
    <div class="card"><div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1"><?= e($m['subject'] ?: 'Website enquiry') ?></h2>
                <div class="text-body-secondary small">From <strong><?= e($m['name']) ?></strong> · <?= e(fmt_date($m['created_at'], 'j M Y, H:i')) ?> · <?= e(time_ago($m['created_at'])) ?></div>
            </div>
            <?= status_badge($m['status']) ?>
        </div>
        <div class="message-body"><?= e($m['body']) ?></div>
        <div class="d-flex flex-wrap gap-3 small text-body-secondary mt-3">
            <span><i class="bi bi-envelope me-1"></i><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></span>
            <?php if ($m['phone']): ?><span><i class="bi bi-telephone me-1"></i><a href="tel:<?= e($m['phone']) ?>"><?= e($m['phone']) ?></a></span><?php endif; ?>
            <span><i class="bi bi-hdd-network me-1"></i>IP <?= e($m['ip'] ?: 'unknown') ?></span>
        </div>
    </div></div>

    <div class="card mt-3">
        <div class="card-header">Reply by email</div>
        <div class="card-body">
            <form method="post" action="<?= $action ?>" data-ajax>
                <?= csrf_field() ?><input type="hidden" name="op" value="reply">
                <div class="small text-body-secondary mb-2">To <strong><?= e($m['email']) ?></strong> · subject “Re: <?= e($m['subject'] ?: 'Your enquiry') ?>”</div>
                <textarea class="form-control mb-3" name="reply" rows="6" placeholder="Write your reply to <?= e($m['name']) ?>…" required></textarea>
                <button class="btn btn-gold"<?= Mailer::configured() ? '' : ' disabled' ?>><i class="bi bi-send me-1"></i>Send reply</button>
                <a class="btn btn-light ms-1" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . ($m['subject'] ?: 'Your enquiry')) ?>">Open in mail app</a>
                <?php if (!Mailer::configured()): ?><span class="small text-danger ms-2">Mail is not configured.</span><?php endif; ?>
            </form>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-3">
        <?php foreach (['new' => ['Mark as unread', 'bi-envelope'], 'replied' => ['Mark as replied', 'bi-check2-all'], 'archived' => ['Archive', 'bi-archive']] as $st => [$label, $ic]): if ($m['status'] === $st) continue; ?>
            <form method="post" action="<?= $action ?>" data-ajax><?= csrf_field() ?><input type="hidden" name="op" value="status"><input type="hidden" name="status" value="<?= $st ?>">
                <button class="btn btn-light btn-sm"><i class="bi <?= $ic ?> me-1"></i><?= e($label) ?></button></form>
        <?php endforeach; ?>
        <form method="post" action="<?= $action ?>" data-ajax data-confirm="Delete this message?"><?= csrf_field() ?><input type="hidden" name="op" value="delete">
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button></form>
    </div>
</div>
