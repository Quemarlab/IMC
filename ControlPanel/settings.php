<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\Settings;

Auth::require('admin');

if (is_post()) {
    admin_post();
    $errors = [];
    foreach (Settings::FIELDS as $key => [$label, , $type]) {
        $value = trim((string) ($_POST[$key] ?? ''));
        if ($type === 'bool') {
            if ($key === 'sec_require_2fa' && isset($_POST[$key]) && empty(Auth::user()['totp_enabled_at'])) {
                $errors[] = 'Turn on two-factor authentication for your own account first (My profile), then enable this rule.';
                continue;
            }
            Settings::set($key, isset($_POST[$key]) ? '1' : '0');
            continue;
        }
        if ($type === 'number') {
            [$min, $max] = ['sec_session_minutes' => [5, 1440], 'sec_lockout_attempts' => [3, 20], 'sec_lockout_minutes' => [1, 1440], 'sec_password_min' => [8, 64], 'newsletter_daily_cap' => [1, 100000]][$key] ?? [0, 1000000];
            if (!ctype_digit($value) || (int) $value < $min || (int) $value > $max) {
                $errors[] = "$label must be a whole number between $min and $max.";
                continue;
            }
        }
        if ($value !== '' && $type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "$label must be a valid email.";
            continue;
        }
        if ($value !== '' && $type === 'url' && !preg_match('#^https://#i', $value)) {
            $errors[] = "$label must start with https://";
            continue;
        }
        if ($key === 'map_embed' && $value !== '' && !str_starts_with($value, 'https://www.google.com/maps/embed')) {
            $errors[] = 'The map must be a Google Maps embed URL (https://www.google.com/maps/embed…).';
            continue;
        }
        Settings::set($key, mb_substr($value, 0, 1000));
    }
    Audit::log('update', 'settings', null, 'Site settings');
    $errors ? flash('error', implode(' ', $errors)) : flash('success', 'Settings saved. They are live on the website now.');
    redirect(admin_url('settings'));
}

$groups = [
    'Company' => ['company_name', 'tagline', 'about_short'],
    'Contact details' => ['phone', 'whatsapp', 'email', 'address', 'hours'],
    'Social media' => ['linkedin', 'facebook', 'twitter', 'instagram', 'youtube'],
    'Map' => ['map_embed'],
    'Newsletter' => ['newsletter_enabled', 'newsletter_daily_cap'],
    'Security — applies to the website admin and UMP' => ['sec_session_minutes', 'sec_lockout_attempts', 'sec_lockout_minutes', 'sec_password_min', 'sec_require_2fa'],
];
admin_header('Site settings', 'settings');
?>
<form method="post" action="<?= admin_url('settings') ?>" style="max-width:820px">
    <?= csrf_field() ?>
    <?php foreach ($groups as $title => $keys): ?>
        <div class="card mb-4">
            <div class="card-header"><?= e($title) ?></div>
            <div class="card-body p-4"><div class="row g-3">
                <?php foreach ($keys as $key): [$label, $default, $type] = Settings::FIELDS[$key]; $current = Settings::all()[$key] ?? ''; ?>
                    <div class="<?= $type === 'textarea' || $key === 'map_embed' ? 'col-12' : 'col-md-6' ?>">
                        <?php if ($type !== 'bool'): ?><label class="form-label" for="s_<?= $key ?>"><?= e($label) ?></label><?php endif; ?>
                        <?php if ($type === 'bool'): ?>
                            <div class="form-check form-switch mt-2"><input class="form-check-input" type="checkbox" role="switch" id="s_<?= $key ?>" name="<?= $key ?>" value="1"<?= ($current !== '' ? $current : $default) === '1' ? ' checked' : '' ?>>
                                <label class="form-check-label" for="s_<?= $key ?>"><?= e($label) ?></label></div>
                        <?php elseif ($type === 'textarea'): ?>
                            <textarea class="form-control" id="s_<?= $key ?>" name="<?= $key ?>" rows="3" placeholder="<?= e($default) ?>"><?= e($current) ?></textarea>
                        <?php else: ?>
                            <input class="form-control" type="<?= $type === 'url' ? 'url' : ($type === 'email' ? 'email' : ($type === 'number' ? 'number' : 'text')) ?>" id="s_<?= $key ?>" name="<?= $key ?>" value="<?= e($current) ?>" placeholder="<?= e($default) ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div></div>
        </div>
    <?php endforeach; ?>
    <p class="text-body-secondary small">Blank fields fall back to the built-in defaults shown as placeholders.</p>
    <button class="btn btn-gold"><i class="bi bi-check2 me-1"></i>Save settings</button>
</form>
<?php admin_footer();
