<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\DB;
use YM\Core\Validator;
use YM\Repositories\Users;

$me = Auth::require('admin');
$id = (int) input('id');
$action = input('action', 'list');

if (is_post()) {
    admin_post();
    $op = input('op', 'save');
    $target = $id ? Users::find($id) : null;

    if ($op === 'delete') {
        if (!$target) {
            redirect(admin_url('users'));
        }
        if ($target['id'] === $me['id']) {
            flash('error', 'You cannot delete your own account.');
        } elseif ($target['role'] === 'admin' && Users::activeAdmins($id) === 0) {
            flash('error', 'At least one active administrator must remain.');
        } else {
            Users::delete($id);
            Audit::log('delete', 'user', $id, $target['email']);
            flash('success', 'User deleted.');
        }
        redirect(admin_url('users'));
    }

    $v = new Validator($_POST, ['name' => 'required|max:120', 'email' => 'required|email|max:190', 'role' => 'required|in:admin,editor', 'status' => 'in:active,disabled']);
    $errors = $v->errors();
    $d = $v->data();
    $d['status'] = $d['status'] ?: 'active';
    $pw = $_POST['password'] ?? '';

    if (!$errors && Users::emailTaken($d['email'], $id)) {
        $errors['email'] = 'That email is already in use.';
    }
    if (!$errors && ($pw !== '' || !$target)) {
        if ($e = Auth::passwordError($pw)) {
            $errors['password'] = $e;
        }
    }
    if (!$errors && $target && $target['role'] === 'admin' && ($d['role'] !== 'admin' || $d['status'] !== 'active') && Users::activeAdmins($id) === 0) {
        $errors['role'] = 'At least one active administrator must remain.';
    }
    if (!$errors && $target && $target['id'] === $me['id'] && ($d['role'] !== 'admin' || $d['status'] !== 'active')) {
        $errors['role'] = 'You cannot demote or disable your own account.';
    }

    if ($errors) {
        $_SESSION['_old'] = $d;
        flash('error', implode(' ', $errors));
        redirect(admin_url('users?action=' . ($id ? 'edit&id=' . $id : 'new')));
    }

    if ($target) {
        $upd = ['name' => $d['name'], 'email' => strtolower($d['email']), 'role' => $d['role'], 'status' => $d['status']];
        if ($pw !== '') {
            $upd['password_hash'] = password_hash($pw, PASSWORD_DEFAULT);
        }
        DB::update('users', $id, $upd);
        Audit::log('update', 'user', $id, $d['email']);
        flash('success', 'User updated.');
    } else {
        $newId = Users::create($d['name'], $d['email'], $pw, $d['role']);
        Audit::log('create', 'user', $newId, $d['email']);
        flash('success', 'User created.');
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
    $val = fn(string $k, string $default = '') => $o[$k] ?? ($u[$k] ?? $default);

    admin_header($u ? 'Edit user' : 'New user', 'users');
    ?>
    <form method="post" action="<?= admin_url('users?action=' . $action . ($u ? '&id=' . $id : '')) ?>" class="card" style="max-width:640px">
        <?= csrf_field() ?>
        <div class="card-body p-4">
            <div class="mb-3"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="<?= e($val('name')) ?>" required maxlength="120"></div>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="email" value="<?= e($val('email')) ?>" required maxlength="190"></div>
            <div class="row g-3 mb-3">
                <div class="col-sm-6"><label class="form-label" for="role">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="editor"<?= $val('role', 'editor') === 'editor' ? ' selected' : '' ?>>Editor — content &amp; messages</option>
                        <option value="admin"<?= $val('role') === 'admin' ? ' selected' : '' ?>>Administrator — everything</option>
                    </select></div>
                <div class="col-sm-6"><label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active"<?= $val('status', 'active') === 'active' ? ' selected' : '' ?>>Active</option>
                        <option value="disabled"<?= $val('status') === 'disabled' ? ' selected' : '' ?>>Disabled</option>
                    </select></div>
            </div>
            <div class="mb-4"><label class="form-label" for="password"><?= $u ? 'New password' : 'Password' ?></label>
                <input class="form-control" type="password" id="password" name="password" autocomplete="new-password" <?= $u ? '' : 'required' ?>>
                <div class="form-text"><?= $u ? 'Leave blank to keep the current password. ' : '' ?>At least 10 characters with letters and numbers.</div></div>
            <button class="btn btn-gold"><?= $u ? 'Save changes' : 'Create user' ?></button>
            <a class="btn btn-link text-body-secondary" href="<?= admin_url('users') ?>">Cancel</a>
        </div>
    </form>
    <?php
    admin_footer();
    exit;
}

$users = Users::all();
admin_header('Users', 'users');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-body-secondary mb-0">People who can sign in to this panel.</p>
    <a class="btn btn-gold" href="<?= admin_url('users?action=new') ?>"><i class="bi bi-person-plus me-1"></i>New user</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover">
    <thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Last sign-in</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><div class="d-flex align-items-center gap-2"><span class="avatar" style="width:36px;height:36px"><?= e(strtoupper(mb_substr($u['name'], 0, 1))) ?></span>
                <div><a class="row-title" href="<?= admin_url('users?action=edit&id=' . $u['id']) ?>"><?= e($u['name']) ?><?= $u['id'] === $me['id'] ? ' <span class="badge text-bg-light">you</span>' : '' ?></a>
                <div class="small text-body-secondary"><?= e($u['email']) ?></div></div></div></td>
            <td><?= e(ucfirst($u['role'])) ?></td><td><?= status_badge($u['status']) ?></td>
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
