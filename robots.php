<?php
require __DIR__ . '/app/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\nDisallow: " . url('ControlPanel/') . "\nDisallow: " . url('api/') . "\n\nSitemap: " . abs_url('sitemap.xml') . "\n";
