<?php
$page = ['title' => 'Privacy policy', 'description' => 'How Young Masters LTD collects, uses and protects personal information submitted through this website.'];
require __DIR__ . '/inc/header.php';

$contact = setting('email');
$hero = ['title' => 'Privacy policy', 'lead' => 'Last updated ' . date('j F Y', filemtime(__FILE__)), 'crumbs' => [['Privacy policy', null]]];
include __DIR__ . '/inc/page-hero.php';
?>
<section class="section">
    <div class="container">
        <div class="row justify-content-center"><div class="col-lg-8 prose">
            <p>This policy explains what personal information <?= e($company) ?> ("we", "us") collects through this website and how we handle it.</p>

            <h2>Information we collect</h2>
            <ul>
                <li><strong>Contact form:</strong> your name, email address, phone number (optional), subject and message.</li>
                <li><strong>Newsletter:</strong> your email address.</li>
                <li><strong>Technical data:</strong> your IP address is stored with form submissions and used to limit abuse. Our server may keep standard access logs.</li>
            </ul>

            <h2>How we use it</h2>
            <p>We use your details only to reply to your enquiry, send the newsletter you asked for, and keep the website secure. We do not sell your information or use it for advertising.</p>

            <h2>Cookies</h2>
            <p>We use a single session cookie that protects forms against forgery. We do not use advertising or tracking cookies. The website loads fonts from Google Fonts, which may receive your IP address.</p>

            <h2>Retention and sharing</h2>
            <p>Enquiries are kept as long as needed to respond and keep a record of our business correspondence. Newsletter subscribers stay on the list until they unsubscribe using the link in any email. We share data only with the service providers needed to run the website and send email, or when the law requires it.</p>

            <h2>Your rights</h2>
            <p>You may ask us to access, correct or delete the personal information we hold about you, and you can unsubscribe from the newsletter at any time. Contact us at <a href="mailto:<?= e($contact) ?>"><?= e($contact) ?></a>.</p>
        </div></div>
    </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
