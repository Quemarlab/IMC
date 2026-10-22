<?php
declare(strict_types=1);

// Scheduled newsletter sending. Run every 5 minutes from cron (cPanel -> Cron Jobs), e.g.
//   */5 * * * *  /usr/local/bin/php /home/USER/public_html/bin/newsletter.php
// Announces scheduled articles when their date arrives and keeps sending large lists within the daily limit.
// Needs APP_URL in .env so the links in the emails point to the real website.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use YM\Core\Newsletter;

$made = Newsletter::queueDue();
$r = Newsletter::process(50);
echo date('c') . "  new campaigns: $made, sent: {$r['sent']}, failed: {$r['failed']}"
    . ($r['capped'] ? ', daily limit reached' : '') . ($r['skipped'] ? ", skipped: {$r['skipped']}" : '') . "\n";