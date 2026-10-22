<?php
declare(strict_types=1);

/**
 * CLI installer / migrator.
 *
 *   php database/install.php                       create tables + seed default services
 *   php database/install.php --admin=you@mail.com  also create (or reset) an admin; prints a generated password
 *   php database/install.php --migrate-legacy      copy data from the old IMC-era tables (project, news, file,
 *                                                  gallery, contact, newslater, account_holders) into the new ones
 *
 * Legacy tables are never modified or dropped.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use YM\Core\DB;
use YM\Core\Migrator;
use YM\Repositories\Users;

$opts = getopt('', ['admin::', 'name::', 'migrate-legacy']);

// 1. Schema ------------------------------------------------------------------
$sql = preg_replace('/^\s*--.*$/m', '', (string) file_get_contents(__DIR__ . '/schema.sql'));
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    DB::pdo()->exec($statement);
}
foreach (Migrator::run(__DIR__ . '/migrations') as $id) {
    echo "✔ Migration applied: $id\n";
}
echo "✔ Schema ready\n";

// 2. Seed services -------------------------------------------------------------
if ((int) DB::val('SELECT COUNT(*) FROM services') === 0) {
    $services = [
        ['Occupational Health & Safety (OHS)', 'bi-heart-pulse', 'Safety systems, risk assessments and on-site training that keep people healthy and operations running.',
            '<p>We help mining operators build and sustain a working safety culture: hazard identification, risk assessments, safe-work procedures, incident investigation and emergency preparedness.</p><p>Our consultants combine field experience in Rwanda\'s mining sector with formal OHS training, so recommendations are practical and enforceable on site.</p>'],
        ['Environmental Management', 'bi-tree', 'Environmental impact work, rehabilitation planning and monitoring aligned with national requirements.',
            '<p>From baseline studies to rehabilitation and closure planning, we support operators and cooperatives in managing their environmental footprint responsibly.</p><p>We prepare management plans, set up monitoring routines and advise on water, waste and land-restoration practice.</p>'],
        ['Mining Compliance', 'bi-patch-check', 'Licensing, regulatory reporting and responsible-sourcing support for mining operators and cooperatives.',
            '<p>Compliance should not slow production down. We map your obligations, close documentation gaps and prepare you for inspections and audits.</p><p>Our work also supports responsible mineral sourcing and traceability requirements demanded by buyers.</p>'],
        ['Geological Services', 'bi-gem', 'Geological mapping, exploration support and technical advice grounded in field experience.',
            '<p>Our geologists support exploration programmes, geological mapping and resource understanding so decisions about where and how to mine rest on solid evidence.</p>'],
        ['Mine Planning', 'bi-map', 'Practical mine design and planning that improves safety, productivity and recovery.',
            '<p>We help operators plan workings that are safe, efficient and realistic for their equipment and workforce — from layout and ventilation to production sequencing.</p>'],
        ['Capacity Building & Training', 'bi-mortarboard', 'Hands-on training programmes for miners, supervisors, cooperatives and institutions.',
            '<p>Our training programmes turn policy into daily practice. Courses are delivered on site or in the classroom, in the language your team works in, and are tailored to the roles of the participants.</p><p>Programmes cover OHS, environmental practice, compliance and supervisory skills.</p>'],
    ];
    foreach ($services as $i => [$title, $icon, $summary, $body]) {
        DB::insert('services', [
            'title' => $title,
            'slug' => slugify($title),
            'icon' => $icon,
            'summary' => $summary,
            'body' => $body,
            'sort_order' => $i + 1,
        ]);
    }
    echo "✔ Seeded " . count($services) . " services\n";
}

// 3. Admin account ------------------------------------------------------------
if (!empty($opts['admin'])) {
    $email = strtolower((string) $opts['admin']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        fwrite(STDERR, "Invalid email for --admin\n");
        exit(1);
    }
    $password = bin2hex(random_bytes(6)) . 'A7';
    $existing = DB::one('SELECT id FROM users WHERE email = ?', [$email]);
    if ($existing) {
        DB::update('users', (int) $existing['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT), 'status' => 'active']);
        Users::setAccess((int) $existing['id'], 'site', 'admin');
    } else {
        Users::create($opts['name'] ?? 'Administrator', $email, $password, ['site' => 'admin']);
    }
    echo "✔ Admin ready: $email\n  Temporary password: $password\n  (change it after signing in)\n";
}

// 4. Legacy migration ---------------------------------------------------------
if (isset($opts['migrate-legacy'])) {
    $has = static fn(string $t): bool => (bool) DB::val('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?', [$t]);
    $copyFiles = static function (string $oldDir, string $newDir, ?string $file): ?string {
        if (!$file) {
            return null;
        }
        $from = YM_ROOT . "/ControlPanel/upload/$oldDir/" . basename($file);
        $dir = YM_ROOT . "/ControlPanel/upload/$newDir";
        if (!is_file($from)) {
            return null;
        }
        @mkdir($dir, 0755, true);
        $name = bin2hex(random_bytes(10)) . '.' . strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return copy($from, "$dir/$name") ? $name : null;
    };
    $firstFile = static fn(string $code): ?string => DB::val('SELECT file FROM file WHERE file_code = ? ORDER BY id LIMIT 1', [$code]);

    if ($has('account_holders')) {
        $n = 0;
        foreach (DB::all("SELECT * FROM account_holders WHERE privilege = 'management'") as $u) {
            if (DB::val('SELECT id FROM users WHERE email = ?', [strtolower($u['email'])])) {
                continue;
            }
            $uid = DB::insert('users', [
                'name' => trim($u['firstname'] . ' ' . $u['lastname']),
                'email' => strtolower($u['email']),
                'password_hash' => $u['password'],
                'status' => $u['status'] === 'active' ? 'active' : 'disabled',
            ]);
            Users::setAccess($uid, 'site', 'admin');
            $n++;
        }
        echo "✔ Users migrated: $n (non-management demo accounts are intentionally skipped)\n";
    }
    if ($has('project') && $has('file')) {
        $n = 0;
        foreach (DB::all('SELECT * FROM project ORDER BY id') as $p) {
            DB::insert('projects', [
                'title' => $p['title'],
                'slug' => \YM\Repositories\Projects::uniqueSlug($p['title']),
                'summary' => excerpt(htmlspecialchars_decode($p['content']), 200),
                'body' => \YM\Core\Sanitizer::clean(htmlspecialchars_decode($p['content'])),
                'cover_image' => $copyFiles('project', 'projects', $firstFile($p['code'])),
                'created_at' => $p['date'],
            ]);
            $n++;
        }
        echo "✔ Projects migrated: $n\n";
    }
    if ($has('news') && $has('file')) {
        $n = 0;
        foreach (DB::all('SELECT * FROM news ORDER BY id') as $p) {
            $body = \YM\Core\Sanitizer::clean(htmlspecialchars_decode((string) $p['content']));
            DB::insert('posts', [
                'title' => (string) $p['title'],
                'slug' => \YM\Repositories\Posts::uniqueSlug((string) $p['title']),
                'excerpt' => excerpt($body, 200),
                'body' => $body,
                'cover_image' => $copyFiles('news', 'news', DB::val('SELECT file FROM file WHERE file_code = ? ORDER BY id DESC LIMIT 1', [$p['code']])),
                'published_at' => $p['created_at'],
                'created_at' => $p['created_at'],
            ]);
            $n++;
        }
        echo "✔ News migrated: $n\n";
    }
    if ($has('gallery') && $has('file')) {
        $n = 0;
        foreach (DB::all('SELECT * FROM gallery ORDER BY id') as $g) {
            foreach (DB::all('SELECT file FROM file WHERE file_code = ?', [$g['code']]) as $f) {
                if ($img = $copyFiles('gallery', 'gallery', $f['file'])) {
                    DB::insert('gallery_items', ['image' => $img, 'created_at' => $g['date']]);
                    $n++;
                }
            }
        }
        echo "✔ Gallery images migrated: $n\n";
    }
    if ($has('contact')) {
        $n = 0;
        foreach (DB::all('SELECT * FROM contact ORDER BY id') as $c) {
            if (DB::val('SELECT id FROM messages WHERE email = ? AND created_at = ? AND name = ?', [$c['email'], $c['date'], html_entity_decode($c['name'])])) {
                continue;
            }
            DB::insert('messages', [
                'name' => html_entity_decode($c['name']),
                'email' => $c['email'],
                'phone' => html_entity_decode($c['phone']),
                'subject' => $c['address'] !== '' ? 'Address: ' . html_entity_decode($c['address']) : 'Website enquiry',
                'body' => html_entity_decode($c['note']),
                'status' => 'read',
                'created_at' => $c['date'],
            ]);
            $n++;
        }
        echo "✔ Contact messages migrated: $n\n";
    }
    if ($has('newslater')) {
        $n = 0;
        foreach (DB::all('SELECT * FROM newslater') as $s) {
            if (!DB::val('SELECT id FROM subscribers WHERE email = ?', [strtolower($s['email'])])) {
                DB::insert('subscribers', ['email' => strtolower($s['email']), 'token' => bin2hex(random_bytes(16)), 'created_at' => $s['created_at']]);
                $n++;
            }
        }
        echo "✔ Subscribers migrated: $n\n";
    }
}

echo "Done.\n";
