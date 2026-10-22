<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';
require __DIR__ . '/inc/resources.php';

use YM\Core\Audit;
use YM\Core\Auth;
use YM\Core\Newsletter;
use YM\Core\Sanitizer;
use YM\Core\Upload;
use YM\Core\Validator;

$user = Auth::require();

$r = input('r');
$cfg = resources()[$r] ?? null;
if (!$cfg) {
    http_response_code(404);
    exit('Unknown content type.');
}
/** @var class-string<\YM\Repositories\Repository> $repo */
$repo = $cfg['repo'];
$self = admin_url('resource?r=' . $r);
$action = input('action', 'list');
$id = (int) input('id');

// ---------------------------------------------------------------- POST handlers
if (is_post()) {
    admin_post();
    $op = input('op', 'save');

    if ($op === 'delete' && $id && $repo::find($id)) {
        $row = $repo::find($id);
        $repo::delete($id);
        Audit::log('delete', $r, $id, $row['title'] ?? ($row['image'] ?? ''));
        flash('success', ucfirst($cfg['singular']) . ' deleted.');
        redirect($self);
    }

    if ($op === 'toggle' && $id && ($row = $repo::find($id))) {
        $repo::update($id, ['is_published' => (int) !$row['is_published']]);
        Audit::log($row['is_published'] ? 'unpublish' : 'publish', $r, $id, $row['title'] ?? '');
        flash('success', 'Visibility updated.');
        if (in_array($r, ['news', 'projects'], true)) {
            redirect_then($self, static function () { Newsletter::queueDue(); Newsletter::process(25); });
        }
        redirect($self);
    }

    // ---- save (create or update)
    $existing = $id ? $repo::find($id) : null;
    if ($id && !$existing) {
        flash('error', 'That item no longer exists.');
        redirect($self);
    }

    $textRules = [];
    foreach ($cfg['fields'] as [$name, , $type, $o]) {
        if (in_array($type, ['text', 'textarea', 'date', 'datetime', 'number'], true) && !empty($o['rules'])) {
            $textRules[$name] = $o['rules'];
        }
    }
    $v = new Validator($_POST, $textRules);
    $errors = $v->errors();
    $data = [];
    $newFiles = [];

    foreach ($cfg['fields'] as [$name, $label, $type, $o]) {
        $raw = $v->data()[$name] ?? input($name);
        switch ($type) {
            case 'checkbox':
                $data[$name] = isset($_POST[$name]) ? 1 : 0;
                break;
            case 'richtext':
                $html = Sanitizer::clean($_POST[$name] ?? '');
                if (!empty($o['rules']) && str_contains($o['rules'], 'required') && trim(strip_tags($html)) === '') {
                    $errors[$name] = "$label is required.";
                }
                $data[$name] = $html;
                break;
            case 'number':
                $data[$name] = (int) $raw;
                break;
            case 'date':
                $data[$name] = $raw !== '' && strtotime($raw) ? date('Y-m-d', strtotime($raw)) : null;
                break;
            case 'datetime':
                $data[$name] = $raw !== '' && strtotime($raw) ? date('Y-m-d H:i:s', strtotime($raw)) : null;
                break;
            case 'image':
                try {
                    $stored = Upload::image($_FILES[$name] ?? ['error' => UPLOAD_ERR_NO_FILE], $repo::folder());
                } catch (RuntimeException $e) {
                    $errors[$name] = $e->getMessage();
                    break;
                }
                if ($stored) {
                    $newFiles[] = $stored;
                    $data[$name] = $stored;
                } elseif (!empty($_POST['remove_' . $name]) && empty($o['required_on_create'])) {
                    $data[$name] = null;
                } elseif (!$existing && !empty($o['required_on_create'])) {
                    $errors[$name] = "$label is required.";
                }
                break;
            default:
                if (!empty($o['pattern']) && $raw !== '' && !preg_match($o['pattern'], $raw)) {
                    $errors[$name] = "$label has an invalid format.";
                }
                $data[$name] = $raw;
        }
    }

    if ($errors) {
        foreach ($newFiles as $f) {
            Upload::delete($repo::folder(), $f); // don't orphan files from a rejected submit
        }
        $_SESSION['_old'] = $_POST;
        flash('error', implode(' ', $errors));
        redirect($self . '&action=' . ($id ? 'edit&id=' . $id : 'new'));
    }

    // Per-type adjustments
    if ($r === 'news') {
        if (!$data['published_at'] && $data['is_published']) {
            $data['published_at'] = $existing['published_at'] ?? date('Y-m-d H:i:s');
        }
        if ($data['excerpt'] === '') {
            $data['excerpt'] = excerpt($data['body'], 200);
        }
        if (!$existing) {
            $data['author_id'] = $user['id'];
        }
    }
    if ($r === 'projects' && $data['summary'] === '') {
        $data['summary'] = excerpt($data['body'], 200);
    }

    foreach (['cover_image', 'image'] as $imgCol) {
        if ($existing && array_key_exists($imgCol, $data) && $data[$imgCol] !== ($existing[$imgCol] ?? null)) {
            Upload::delete($repo::folder(), $existing[$imgCol] ?? null); // replaced or removed
        }
    }

    if ($existing) {
        if (array_key_exists('slug', $data) && $data['slug'] === '') {
            unset($data['slug']); // keep the current URL
        }
        $repo::update($id, $data);
        Audit::log('update', $r, $id, $data['title'] ?? '');
        flash('success', ucfirst($cfg['singular']) . ' updated.');
    } else {
        if (array_key_exists('slug', $data) && $data['slug'] === '') {
            $data['slug'] = $data['title'];
        }
        $id = $repo::create($data);
        Audit::log('create', $r, $id, $data['title'] ?? '');
        flash('success', ucfirst($cfg['singular']) . ' created.');
    }
    unset($_SESSION['_old']);
    $to = $self . (!empty($_POST['add_another']) ? '&action=new' : '');
    if (in_array($r, ['news', 'projects'], true)) {
        if (!empty($data['notify_subscribers']) && !empty($data['is_published']) && empty($existing['notified_at'])) {
            $future = $r === 'news' && !empty($data['published_at']) && strtotime($data['published_at']) > time();
            flash('info', Newsletter::enabled()
                ? ($future ? 'Subscribers will be emailed on the publish date.' : 'Subscribers are being emailed now — follow progress under Newsletter.')
                : 'Subscribers were not emailed: mail or the newsletter is switched off (see Settings).');
        }
        redirect_then($to, static function () { Newsletter::queueDue(); Newsletter::process(25); });
    }
    redirect($to);
}

// ---------------------------------------------------------------- views
if ($action === 'new' || $action === 'edit') {
    $row = $action === 'edit' ? $repo::find($id) : null;
    if ($action === 'edit' && !$row) {
        http_response_code(404);
        exit('Not found.');
    }
    $oldInput = $_SESSION['_old'] ?? null;
    unset($_SESSION['_old']);

    // Field value: sticky input > stored row > configured default
    $value = static function (string $name, array $o) use ($row, $oldInput) {
        if ($oldInput !== null && array_key_exists($name, $oldInput)) {
            return $oldInput[$name];
        }
        if ($row !== null) {
            $v = $row[$name] ?? '';
            return $v;
        }
        return $o['default'] ?? '';
    };

    $renderField = static function (array $f) use ($value, $row, $oldInput, $cfg): void {
        [$name, $label, $type, $o] = $f;
        $val = $value($name, $o);
        $help = !empty($o['help']) ? '<div class="form-text">' . e($o['help']) . '</div>' : '';
        $req = !empty($o['rules']) && str_contains($o['rules'], 'required') ? ' <span class="text-danger">*</span>' : '';
        echo '<div class="mb-3">';
        switch ($type) {
            case 'checkbox':
                $checked = $oldInput !== null ? isset($oldInput[$name]) : (bool) $val;
                echo '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="f_' . $name . '" name="' . $name . '" value="1"' . ($checked ? ' checked' : '') . '>'
                    . '<label class="form-check-label fw-semibold" for="f_' . $name . '">' . e($label) . '</label></div>';
                if ($name === 'notify_subscribers') {
                    echo !empty($row['notified_at'])
                        ? '<div class="form-text text-success"><i class="bi bi-check2-circle"></i> Subscribers were emailed on ' . e(fmt_date($row['notified_at'], 'j M Y, H:i')) . '. Saving again will not email them twice.</div>'
                        : ($help ? str_replace('<div class="form-text">', '<div class="form-text">', $help) : '');
                    $help = '';
                }
                break;
            case 'textarea':
                echo '<label class="form-label" for="f_' . $name . '">' . e($label) . $req . '</label><textarea class="form-control" id="f_' . $name . '" name="' . $name . '" rows="' . (int) ($o['rows'] ?? 4) . '"'
                    . (isset($o['maxlength']) ? ' maxlength="' . (int) $o['maxlength'] . '"' : '') . '>' . e($val) . '</textarea>' . $help;
                break;
            case 'richtext':
                echo '<label class="form-label">' . e($label) . $req . '</label><textarea class="richtext" name="' . $name . '" placeholder="Start writing…">' . e($val) . '</textarea>' . $help;
                break;
            case 'image':
                $folder = $cfg['repo']::folder();
                $current = $row[$name] ?? null;
                echo '<label class="form-label">' . e($label) . $req . '</label>'
                    . '<img class="img-preview" id="prev_' . $name . '" alt="" src="' . ($current ? e(upload_url($folder, $current)) : '') . '">'
                    . '<input class="form-control" type="file" name="' . $name . '" accept="image/jpeg,image/png,image/webp" data-preview="prev_' . $name . '">'
                    . '<div class="form-text">JPG, PNG or WebP, up to 8 MB.</div>';
                if ($current && empty($o['required_on_create'])) {
                    echo '<div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_' . $name . '" value="1" id="rm_' . $name . '"><label class="form-check-label" for="rm_' . $name . '">Remove current image</label></div>';
                }
                break;
            default:
                $inputType = ['date' => 'date', 'datetime' => 'datetime-local', 'number' => 'number'][$type] ?? 'text';
                if ($type === 'date' && $val) {
                    $val = date('Y-m-d', strtotime((string) $val));
                }
                if ($type === 'datetime' && $val) {
                    $val = date('Y-m-d\TH:i', strtotime((string) $val));
                }
                echo '<label class="form-label" for="f_' . $name . '">' . e($label) . $req . '</label><input class="form-control" type="' . $inputType . '" id="f_' . $name . '" name="' . $name . '" value="' . e($val) . '"'
                    . (isset($o['maxlength']) ? ' maxlength="' . (int) $o['maxlength'] . '"' : '') . '>' . $help;
        }
        echo '</div>';
    };

    $main = array_filter($cfg['fields'], fn($f) => empty($f[3]['side']));
    $side = array_filter($cfg['fields'], fn($f) => !empty($f[3]['side']));
    $hasSide = (bool) $side;

    admin_header(($row ? "Edit " : "New ") . $cfg["singular"], $cfg["nav"], "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css\">");
    ?>
    <form method="post" enctype="multipart/form-data" action="<?= e($self . '&action=' . $action . ($row ? '&id=' . $id : '')) ?>">
        <?= csrf_field() ?>
        <div class="row g-4">
            <div class="col-lg-<?= $hasSide ? 8 : 7 ?>">
                <div class="card"><div class="card-body p-4">
                    <?php foreach ($main as $f) { $renderField($f); } ?>
                </div></div>
            </div>
            <div class="col-lg-<?= $hasSide ? 4 : 5 ?>">
                <div class="card position-sticky" style="top:86px">
                    <div class="card-header">Publish</div>
                    <div class="card-body">
                        <?php foreach ($side as $f) { $renderField($f); } ?>
                        <div class="d-grid gap-2 mt-2">
                            <button class="btn btn-gold" type="submit"><i class="bi bi-check2 me-1"></i><?= $row ? 'Save changes' : 'Create ' . e($cfg['singular']) ?></button>
                            <?php if (!$row): ?><button class="btn btn-light" type="submit" name="add_another" value="1">Save &amp; add another</button><?php endif; ?>
                            <a class="btn btn-link text-body-secondary" href="<?= e($self) ?>">Cancel</a>
                        </div>
                        <?php if ($row && $cfg['public']): ?>
                            <hr><a class="small" target="_blank" rel="noopener" href="<?= e($cfg['public']($row)) ?>"><i class="bi bi-box-arrow-up-right me-1"></i>View on website</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
    admin_footer(['https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js']);
    exit;
}

// ---- list
$q = input('q');
$filter = input('state');
$where = null;
if (in_array($filter, ['published', 'draft'], true)) {
    $where = 'is_published = ' . ($filter === 'published' ? 1 : 0);
}
$pg = $repo::paginate(['page' => (int) input('page', '1'), 'per' => 12, 'q' => $q, 'where' => $where]);

admin_header($cfg['label'], $cfg['nav']);
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="get">
        <input type="hidden" name="r" value="<?= e($r) ?>">
        <input class="form-control" style="width:240px" type="search" name="q" value="<?= e($q) ?>" placeholder="Search <?= e(strtolower($cfg['label'])) ?>…">
        <select class="form-select" name="state" style="width:150px" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <option value="published"<?= $filter === 'published' ? ' selected' : '' ?>>Published</option>
            <option value="draft"<?= $filter === 'draft' ? ' selected' : '' ?>>Drafts</option>
        </select>
    </form>
    <a class="btn btn-gold" href="<?= e($self . '&action=new') ?>"><i class="bi bi-plus-lg me-1"></i>New <?= e($cfg['singular']) ?></a>
</div>

<div class="card">
    <?php if (!$pg['items']): ?>
        <div class="empty-admin"><i class="bi <?= e($cfg['icon']) ?>"></i><?= ($q || $filter) ? 'Nothing matches your filters.' : 'No ' . e(strtolower($cfg['label'])) . ' yet — create the first one.' ?></div>
    <?php else: ?>
        <div class="table-responsive"><table class="table table-hover align-middle">
            <thead><tr><th style="width:76px"></th><th><?= $r === 'gallery' ? 'Caption' : 'Title' ?></th>
                <?php foreach (array_keys($cfg['columns']) as $h): ?><th><?= e($h) ?></th><?php endforeach; ?><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($pg['items'] as $row): ?>
                <tr>
                    <td><img class="thumb-sm" src="<?= e(upload_url($repo::folder(), $row[$cfg['image']])) ?>" alt="" loading="lazy"></td>
                    <td><a class="row-title" href="<?= e($self . '&action=edit&id=' . $row['id']) ?>"><?= e($row['title'] ?: '(untitled photo)') ?></a></td>
                    <?php foreach ($cfg['columns'] as $render): ?><td><?= $render($row) ?></td><?php endforeach; ?>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-light" title="View on website" target="_blank" rel="noopener" href="<?= e($cfg['public']($row)) ?>"><i class="bi bi-eye"></i></a>
                        <form class="d-inline" method="post" action="<?= e($self) ?>"><?= csrf_field() ?><input type="hidden" name="op" value="toggle"><input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button class="btn btn-sm btn-light" title="<?= $row['is_published'] ? 'Unpublish' : 'Publish' ?>"><i class="bi <?= $row['is_published'] ? 'bi-toggle-on text-success' : 'bi-toggle-off' ?>"></i></button></form>
                        <a class="btn btn-sm btn-light" title="Edit" href="<?= e($self . '&action=edit&id=' . $row['id']) ?>"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="post" action="<?= e($self) ?>" data-confirm="Delete this <?= e($cfg['singular']) ?>?" data-confirm-text="The <?= e($cfg['singular']) ?> and its image will be permanently removed."><?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <div class="card-body py-2 small text-body-secondary d-flex justify-content-between align-items-center">
            <span><?= $pg['total'] ?> item<?= $pg['total'] === 1 ? '' : 's' ?></span>
            <?php $pgClass = 'mb-0'; include __DIR__ . '/../inc/pagination.php'; ?>
        </div>
    <?php endif; ?>
</div>
<?php admin_footer();
