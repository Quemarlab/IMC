<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Services;

header('Content-Type: application/xml; charset=utf-8');

$urls = [['', null, '1.0']];
foreach (['about', 'services', 'projects', 'news', 'gallery', 'contact'] as $p) {
    $urls[] = [$p, null, '0.7'];
}
foreach (Services::paginate(['public' => true, 'per' => 100])['items'] as $r) {
    $urls[] = ['services/' . $r['slug'], $r['updated_at'], '0.8'];
}
foreach (Projects::paginate(['public' => true, 'per' => 100])['items'] as $r) {
    $urls[] = ['projects/' . $r['slug'], $r['updated_at'], '0.6'];
}
foreach (Posts::paginate(['public' => true, 'per' => 100])['items'] as $r) {
    $urls[] = ['news/' . $r['slug'], $r['updated_at'], '0.6'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$path, $mod, $prio]) {
    echo '  <url><loc>' . e(abs_url($path)) . '</loc>' . ($mod ? '<lastmod>' . date('c', strtotime($mod)) . '</lastmod>' : '') . '<priority>' . $prio . "</priority></url>\n";
}
echo '</urlset>';
