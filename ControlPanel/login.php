<?php
declare(strict_types=1);

require __DIR__ . '/inc/auth-layout.php';

use YM\Core\Auth;

if (Auth::check()) {
    redirect(admin_url('dashboard'));
}

if (is_post()) {
    admin_post();
    $result = Auth::attempt(input('email'), $_POST['password'] ?? '');
    if ($result['ok']) {
        $to = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);
        // Only follow same-site, same-panel redirects.
        $panel = base_path() . '/ControlPanel/';
        redirect(is_string($to) && str_starts_with($to, $panel) ? $to : admin_url('dashboard'));
    }
    flash('error', $result['message']);
    $_SESSION['_old'] = ['email' => input('email')];
    redirect(admin_url('login'));
}

$oldEmail = old('email');
unset($_SESSION['_old']);

auth_page('Sign in', function () use ($oldEmail) { ?>
    <h1 class="h3 mb-1">Welcome back</h1>
    <p class="text-body-secondary mb-4">Sign in to the admin panel.</p>
    <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email" value="<?= e($oldEmail) ?>" required autofocus autocomplete="username">
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between"><label class="form-label" for="password">Password</label>
                <a class="small" href="<?= admin_url('forgot-password') ?>">Forgot password?</a></div>
            <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button class="btn btn-gold w-100 py-2 mt-2">Sign in</button>
    </form>
    <p class="text-center mt-4 mb-0"><a class="small text-body-secondary" href="<?= url() ?>">&larr; Back to website</a></p>
<?php });
