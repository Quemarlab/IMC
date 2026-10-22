<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\DB;
use YM\Core\Validator;
use YM\Repositories\Users;

$me = Auth::require();

if (is_post()) {
    admin_post();
    $op = input('op');

    if ($op === 'profile') {
        $v = new Validator($_POST, ['name' => 'required|max:120', 'email' => 'required|email|max:190']);
        if ($v->fails()) {
            flash('error', $v->first());
        } elseif (Users::emailTaken($v->data()['email'], $me['id'])) {
            flash('error', 'That email is already in use.');
        } else {
            DB::update('users', $me['id'], ['name' => $v->data()['name'], 'email' => strtolower($v->data()['email'])]);
            Audit::log('update', 'user', $me['id'], 'profile');
            flash('success', 'Profile updated.');
        }
    } elseif ($op === 'password') {
        $row = DB::one('SELECT password_hash FROM users WHERE id = ?', [$me['id']]);
        $new = $_POST['new_password'] ?? '';
        if (!password_verify($_POST['current_password'] ?? '', $row['password_hash'])) {
            flash('error', 'Your current password is incorrect.');
        } elseif ($e = Auth::passwordError($new)) {
            flash('error', $e);
        } elseif ($new !== ($_POST['confirm_password'] ?? '')) {
            flash('error', 'The new passwords do not match.');
        } else {
            DB::update('users', $me['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            Audit::log('password.change', 'user', $me['id']);
            flash('success', 'Password changed.');
        }
    }
    redirect(admin_url('profile'));
}

admin_header('My profile', '');
?>
<div class="row g-4" style="max-width:1000px">
    <div class="col-md-6">
        <form class="card" method="post" action="<?= admin_url('profile') ?>">
            <div class="card-header">Profile</div>
            <div class="card-body p-4">
                <?= csrf_field() ?><input type="hidden" name="op" value="profile">
                <div class="mb-3"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="<?= e($me['name']) ?>" required></div>
                <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="email" value="<?= e($me['email']) ?>" required></div>
                <div class="mb-4"><span class="form-label d-block">Role</span><span class="badge text-bg-light"><?= e(ucfirst($me['role'])) ?></span></div>
                <button class="btn btn-gold">Save profile</button>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <form class="card" method="post" action="<?= admin_url('profile') ?>">
            <div class="card-header">Change password</div>
            <div class="card-body p-4">
                <?= csrf_field() ?><input type="hidden" name="op" value="password">
                <div class="mb-3"><label class="form-label" for="cp">Current password</label><input class="form-control" type="password" id="cp" name="current_password" required autocomplete="current-password"></div>
                <div class="mb-3"><label class="form-label" for="np">New password</label><input class="form-control" type="password" id="np" name="new_password" required minlength="10" autocomplete="new-password">
                    <div class="form-text">At least 10 characters with letters and numbers.</div></div>
                <div class="mb-4"><label class="form-label" for="cf">Confirm new password</label><input class="form-control" type="password" id="cf" name="confirm_password" required autocomplete="new-password"></div>
                <button class="btn btn-dark">Update password</button>
            </div>
        </form>
    </div>
</div>
<?php admin_footer();
