<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\DB;
use YM\Core\Env;
use YM\Core\Mailer;
use YM\Core\Validator;
use YM\Repositories\Users;

$me = Auth::require('admin');
$id = (int) input('id');
$action = input('action', 'list');

/** Where a new/returning user should land to choose a password: the website admin if they have it, else UMP. */
function invite_link(array $user, string $token): string
{
    $hasSite = Auth::roleFor((int) $user['id'], 'site') !== null;
    $ump = rtrim((string) Env::get('UMP_URL', ''), '/');
    return $hasSite || $ump === '' ? abs_url('ControlPanel/reset-password?token=' . $token) : $ump . '/reset-password?token=' . $token;
}

/** Emails a set-your-password link (valid 3 days). Returns false when mail is not configured or sending failed. */
function send_invite(int $userId, string $reason): bool
{
    $u = Users::find($userId);
    $reset = $u ? Auth::createResetToken($u['email'], 3 * 86400) : null;
    if (!$reset) {
        return false;
    }
    $access = [];
    foreach (Users::APPS as $app => $label) {
        $role = $u[$app === 'site' ? 'site_role' : 'ump_role'];
        if ($role) {
            $access[] = '<li><strong>' . e($label) . '</strong> — ' . e(Users::ROLES[$app][$role]) . '</li>';
        }
    }
    $link = invite_link($u, $reset['token']);
    $by = Auth::user()['name'];
    return Mailer::send(
        $u['email'],
        $reason === 'invite' ? 'You have been invited to ' . setting('company_name') : 'Set a new password',
        '<p>Hello ' . e($u['name']) . ',</p><p>' . ($reason === 'invite'
            ? e($by) . ' created an account for you with this access:'
            : e($by) . ' sent you a link to choose a new password. Your access:') . '</p><ul>' . implode('', $access) . '</ul>'
        . '<p><a href="' . e($link) . '" style="background:#d4a017;color:#1d2124;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600">Choose your password</a></p>'
        . '<p style="color:#888;font-size:13px">The link works for 3 days. Your username is your email address (' . e($u['email']) . ').</p>'
    );
}

if (is_post()) {
    admin_post();
    $op = input('op', 'save');
    $target = $id ? Users::find($id) : null;

    if (in_array($op, ['delete', 'invite', 'reset2fa'], true) && !$target) {
        redirect(admin_url('users'));
    }

    if ($op === 'delete') {
        if ($target['id'] === $me['id']) {
            flash('error', 'You cannot delete your own account.');
        } elseif ($target['site_role'] === 'admin' && Users::activeAdmins($id) === 0) {
            flash('error', 'At least one active administrator must remain.');
        } else {
            Users::delete($id);
            Audit::log('delete', 'user', $id, $target['email']);
            flash('success', 'User deleted.');
        }
        redirect(admin_url('users'));
    }
    if ($op === 'invite') {
        if (send_invite($id, 'reset')) {
            Audit::log('invite', 'user', $id, $target['email']);
            flash('success', 'A password link was emailed to ' . $target['email'] . '.');
        } else {
            flash('error', 'The email could not be sent. Check the mail settings in .env, or set a password for them by editing the account.');
        }
        redirect(admin_url('users?action=edit&id=' . $id));
    }
    if ($op === 'reset2fa') {
        DB::update('users', $id, ['totp_secret' => null, 'totp_enabled_at' => null, 'totp_last_step' => 0, 'recovery_codes' => null]);
        Audit::log('2fa.reset', 'user', $id, $target['email']);
        flash('success', 'Two-factor authentication was turned off for ' . $target['name'] . '. They can set it up again from their profile.');
        redirect(admin_url('users?action=edit&id=' . $id));
    }

    // ---- save (create or update)
    $v = new Validator($_POST, ['name' => 'required|max:120', 'email' => 'required|email|max:190', 'status' => 'in:active,disabled']);
    $errors = $v->errors();
    $d = $v->data();
    $d['status'] = $d['status'] ?: 'active';
    $siteRole = input('site_role');
    $umpRole = input('ump_role');
    $d['site_role'] = $siteRole;
    $d['ump_role'] = $umpRole;
    $d['ump_notify'] = isset($_POST['ump_notify']) ? 1 : 0;
    $mode = input('password_mode', 'invite');
    $pw = $_POST['password'] ?? '';

    if ($siteRole !== '' && !isset(Users::ROLES['site'][$siteRole])) {
        $errors['site_role'] = 'Choose a valid website role.';
    }
    if ($umpRole !== '' && !isset(Users::ROLES['ump'][$umpRole])) {
        $errors['ump_role'] = 'Choose a valid UMP role.';
    }
    if (!$errors && $siteRole === '' && $umpRole === '') {
        $errors['site_role'] = 'Give the account access to at least one system.';
    }
    if (!$errors && Users::emailTaken($d['email'], $id)) {
        $errors['email'] = 'That email is already in use.';
    }
    if (!$errors && ((!$target && $mode === 'password') || ($target && $pw !== ''))) {
        if ($e = Auth::passwordError($pw)) {
            $errors['password'] = $e;
        }
    }
    $wasAdmin = $target && $target['site_role'] === 'admin';
    if (!$errors && $wasAdmin && ($siteRole !== 'admin' || $d['status'] !== 'active') && Users::activeAdmins($id) === 0) {
        $errors['site_role'] = 'At least one active website administrator must remain.';
    }
    if (!$errors && $target && $target['id'] === $me['id'] && ($siteRole !== 'admin' || $d['status'] !== 'active')) {
        $errors['site_role'] = 'You cannot remove your own administrator access or disable your own account.';
    }

    if ($errors) {
        $_SESSION['_old'] = $d;
        flash('error', implode(' ', $errors));
        redirect(admin_url('users?action=' . ($id ? 'edit&id=' . $id : 'new')));
    }

    if ($target) {
        $upd = ['name' => $d['name'], 'email' => strtolower($d['email']), 'status' => $d['status']];
        if ($pw !== '') {
            $upd['password_hash'] = password_hash($pw, PASSWORD_DEFAULT);
            $upd['password_changed_at'] = date('Y-m-d H:i:s');
        }
        DB::update('users', $id, $upd);
        $userId = $id;
        Audit::log('update', 'user', $id, $d['email'] . ' · site=' . ($siteRole ?: 'none') . ' ump=' . ($umpRole ?: 'none'));
        flash('success', 'User updated.');
    } else {
        $initial = $mode === 'password' ? $pw : bin2hex(random_bytes(16)) . 'Aa1';
        $userId = Users::create($d['name'], $d['email'], $initial, []);
        Audit::log('create', 'user', $userId, $d['email'] . ' · site=' . ($siteRole ?: 'none') . ' ump=' . ($umpRole ?: 'none'));
    }
    Users::setAccess($userId, 'site', $siteRole ?: null);
    Users::setAccess($userId, 'ump', $umpRole ?: null, (bool) $d['ump_notify']);

    if (!$target) {
        if ($mode === 'invite') {
            $sent = send_invite($userId, 'invite');
            flash($sent ? 'success' : 'warning', $sent
                ? 'User created and an invitation was emailed to ' . $d['email'] . '.'
                : 'User created, but the invitation email could not be sent. Edit the account to set a password for them.');
        } else {
            flash('success', 'User created. Give them the password you set; they can change it from their profile.');
        }
    }
    redirect(admin_url('users'));
}

if ($action === 'new' || $action === 'edit') {
    $u = $action === 'edit' ? Users::find($id) : null;
    if ($action === 'edit' && !$u) {
        redirect(admin_url('users'));
    }
    $o = $_SESSION['_old'] ?? [];
    unset($_SESSION['_old']);
    $val = fn(string $k, string $default = '') => (string) ($o[$k] ?? ($u[$k] ?? $default));
    $mail = Mailer::configured();
    $roleOptions = function (string $app, string $current, string $none) {
        $h = '<option value="">' . e($none) . '</option>';
        $desc = ['site' => ['admin' => 'everything, including users and security', 'editor' => 'content and messages'],
            'ump' => ['admin' => 'everything in UMP, settings and backups', 'operator' => 'record daily figures and view reports', 'viewer' => 'read-only: dashboard, records, reports']];
        foreach (Users::ROLES[$app] as $r => $label) {
            $h .= '<option value="' . e($r) . '"' . ($current === $r ? ' selected' : '') . '>' . e($label) . ' — ' . e($desc[$app][$r]) . '</option>';
        }
        return $h;
    };

    admin_header($u ? 'Edit user' : 'New user', 'users');
    ?>
    <div class="row g-4">
    <div class="col-xl-8">
    <form method="post" action="<?= admin_url('users?action=' . $action . ($u ? '&id=' . $id : '')) ?>" class="card">
        <?= csrf_field() ?>
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="<?= e($val('name')) ?>" required maxlength="120"></div>
                <div class="col-md-6"><label class="form-label" for="email">Email (sign-in name)</label><input class="form-control" type="email" id="email" name="email" value="<?= e($val('email')) ?>" required maxlength="190"></div>
            </div>

            <div class="form-section-title">What can they access?</div>
            <div class="row g-3 mb-3">
                <div class="col-12"><label class="form-label" for="site_role"><i class="bi bi-globe2 me-1"></i>Website admin</label>
                    <select class="form-select" id="site_role" name="site_role"><?= $roleOptions('site', $val('site_role'), 'No access') ?></select></div>
                <div class="col-12"><label class="form-label" for="ump_role"><i class="bi bi-box-seam me-1"></i>UMP — Unprocessed Materials Production</label>
                    <select class="form-select" id="ump_role" name="ump_role"><?= $roleOptions('ump', $val('ump_role'), 'No access') ?></select></div>
                <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="ump_notify" name="ump_notify" value="1"<?= $val('ump_notify') === '1' ? ' checked' : '' ?>>
                    <label class="form-check-label" for="ump_notify">Email this person the UMP daily summary and alerts</label></div></div>
            </div>

            <div class="mb-3"><label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" style="max-width:220px">
                    <option value="active"<?= $val('status', 'active') === 'active' ? ' selected' : '' ?>>Active</option>
                    <option value="disabled"<?= $val('status') === 'disabled' ? ' selected' : '' ?>>Disabled — cannot sign in anywhere</option>
                </select></div>

            <div class="form-section-title"><?= $u ? 'Password' : 'How do they get their password?' ?></div>
            <?php if (!$u): ?>
                <div class="form-check mb-1"><input class="form-check-input" type="radio" name="password_mode" id="pm_invite" value="invite"<?= $mail ? ' checked' : ' disabled' ?>>
                    <label class="form-check-label" for="pm_invite">Email them a link to choose their own <span class="text-body-secondary">(recommended)</span><?= $mail ? '' : ' — mail is not configured' ?></label></div>
                <div class="form-check mb-3"><input class="form-check-input" type="radio" name="password_mode" id="pm_pw" value="password"<?= $mail ? '' : ' checked' ?>>
                    <label class="form-check-label" for="pm_pw">I will set a password and tell them</label></div>
            <?php endif; ?>
            <div class="mb-4"><label class="form-label" for="password"><?= $u ? 'New password' : 'Password (only if you chose to set one)' ?></label>
                <input class="form-control" type="password" id="password" name="password" autocomplete="new-password">
                <div class="form-text"><?= $u ? 'Leave blank to keep the current password. ' : '' ?>At least <?= Auth::passwordMin() ?> characters with letters and numbers.</div></div>

            <button class="btn btn-gold"><?= $u ? 'Save changes' : 'Create user' ?></button>
            <a class="btn btn-link text-body-secondary" href="<?= admin_url('users') ?>">Cancel</a>
        </div>
    </form>
    </div>

    <?php if ($u): ?>
    <div class="col-xl-4">
        <div class="card mb-3"><div class="card-header">Account</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item bg-transparent d-flex justify-content-between"><span>Last sign-in</span><strong><?= $u['last_login_at'] ? e(time_ago($u['last_login_at'])) : 'Never' ?></strong></li>
                <li class="list-group-item bg-transparent d-flex justify-content-between"><span>Password changed</span><strong><?= $u['password_changed_at'] ? e(fmt_date($u['password_changed_at'])) : '—' ?></strong></li>
                <li class="list-group-item bg-transparent d-flex justify-content-between"><span>Two-factor</span><strong><?= $u['totp_enabled_at'] ? '<span class="text-success">On</span>' : 'Off' ?></strong></li>
            </ul>
            <div class="card-body d-grid gap-2">
                <form method="post" action="<?= admin_url('users?id=' . $id) ?>"><?= csrf_field() ?><input type="hidden" name="op" value="invite">
                    <button class="btn btn-light w-100 text-start"<?= $mail ? '' : ' disabled' ?>><i class="bi bi-envelope me-1"></i>Email a password link</button></form>
                <?php if ($u['totp_enabled_at']): ?>
                    <form method="post" action="<?= admin_url('users?id=' . $id) ?>" data-confirm="Turn off two-factor for <?= e($u['name']) ?>?" data-confirm-text="Use this when they lost their phone and recovery codes. They can set it up again afterwards."><?= csrf_field() ?><input type="hidden" name="op" value="reset2fa">
                        <button class="btn btn-light w-100 text-start"><i class="bi bi-shield-x me-1"></i>Reset two-factor</button></form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    </div>
    <?php
    admin_footer();
    exit;
}

$users = Users::all();
admin_header('Users', 'users');
$badge = fn(?string $role, string $app) => $role
    ? '<span class="badge text-bg-' . ($role === 'admin' ? 'dark' : 'secondary') . '">' . e(Users::ROLES[$app][$role]) . '</span>'
    : '<span class="text-body-secondary small">—</span>';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <p class="text-body-secondary mb-0">One account works in every system. Choose what each person can do in each of them.</p>
    <a class="btn btn-gold" href="<?= admin_url('users?action=new') ?>"><i class="bi bi-person-plus me-1"></i>New user</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover">
    <thead><tr><th>Name</th><th>Website</th><th>UMP</th><th>2FA</th><th>Status</th><th>Last sign-in</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><div class="d-flex align-items-center gap-2"><span class="avatar" style="width:36px;height:36px"><?= e(strtoupper(mb_substr($u['name'], 0, 1))) ?></span>
                <div><a class="row-title" href="<?= admin_url('users?action=edit&id=' . $u['id']) ?>"><?= e($u['name']) ?><?= $u['id'] === $me['id'] ? ' <span class="badge text-bg-light">you</span>' : '' ?></a>
                <div class="small text-body-secondary"><?= e($u['email']) ?></div></div></div></td>
            <td><?= $badge($u['site_role'], 'site') ?></td><td><?= $badge($u['ump_role'], 'ump') ?></td>
            <td><?= $u['totp_enabled_at'] ? '<i class="bi bi-shield-check text-success" title="Two-factor is on"></i>' : '<i class="bi bi-shield text-body-secondary" title="Off"></i>' ?></td>
            <td><?= status_badge($u['status']) ?></td>
            <td class="small text-body-secondary"><?= $u['last_login_at'] ? e(time_ago($u['last_login_at'])) : 'Never' ?></td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-light" href="<?= admin_url('users?action=edit&id=' . $u['id']) ?>"><i class="bi bi-pencil"></i></a>
                <?php if ($u['id'] !== $me['id']): ?>
                    <form class="d-inline" method="post" action="<?= admin_url('users') ?>" data-confirm="Delete <?= e($u['name']) ?>?"><?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button></form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table></div></div>
<?php admin_footer();
