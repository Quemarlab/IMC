<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use YM\Repositories\Posts;

header('Content-Type: application/rss+xml; charset=utf-8');
$items = Posts::paginate(['public' => true, 'per' => 20])['items'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?= e(setting('company_name')) ?> — News</title>
    <link><?= e(abs_url('news')) ?></link>
    <description><?= e(setting('tagline')) ?></description>
    <language>en</language>
    <atom:link href="<?= e(abs_url('feed.xml')) ?>" rel="self" type="application/rss+xml"/>
<?php foreach ($items as $p): $when = $p['published_at'] ?: $p['created_at']; ?>
    <item>
        <title><?= e($p['title']) ?></title>
        <link><?= e(abs_url('news/' . $p['slug'])) ?></link>
        <guid isPermaLink="true"><?= e(abs_url('news/' . $p['slug'])) ?></guid>
        <pubDate><?= date(DATE_RSS, strtotime($when)) ?></pubDate>
        <category><?= e($p['category']) ?></category>
        <description><?= e($p['excerpt'] ?: excerpt($p['body'], 300)) ?></description>
    </item>
<?php endforeach; ?>
</channel>
</rss>
