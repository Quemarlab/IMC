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
                        <label class="form-label" for="s_<?= $key ?>"><?= e($label) ?></label>
                        <?php if ($type === 'textarea'): ?>
                            <textarea class="form-control" id="s_<?= $key ?>" name="<?= $key ?>" rows="3" placeholder="<?= e($default) ?>"><?= e($current) ?></textarea>
                        <?php else: ?>
                            <input class="form-control" type="<?= $type === 'url' ? 'url' : ($type === 'email' ? 'email' : 'text') ?>" id="s_<?= $key ?>" name="<?= $key ?>" value="<?= e($current) ?>" placeholder="<?= e($default) ?>">
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
