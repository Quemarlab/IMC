<?php
declare(strict_types=1);

require __DIR__ . '/inc/layout.php';
require __DIR__ . '/inc/resources.php';

use YM\Core\Auth;
use YM\Core\DB;
use YM\Repositories\Messages;
use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Services;
use YM\Repositories\Subscribers;

// JSON lookups for the Ctrl+K quick search: content, messages, subscribers and (for administrators) users.
if (!Auth::peek()) {
    json_response(['ok' => false], 401);
}
$me = Auth::require();

$q = mb_substr(trim(input('q')), 0, 60);
$groups = [];
if (mb_strlen($q) >= 2) {
    $add = static function (string $name, string $icon, array $items) use (&$groups): void {
        if ($items) {
            $groups[] = ['name' => $name, 'icon' => $icon, 'items' => $items];
        }
    };
    $content = static function (string $repo, string $res, string $subField) use ($q): array {
        $out = [];
        foreach ($repo::paginate(['q' => $q, 'per' => 4])['items'] as $r) {
            $draft = !(int) ($r['is_published'] ?? 1);
            $out[] = ['label' => $r['title'], 'sub' => trim(($draft ? 'Draft · ' : '') . ($r[$subField] ?? '')), 'url' => admin_url('resource?r=' . $res . '&action=edit&id=' . $r['id'])];
        }
        return $out;
    };
    $add('News', 'bi-newspaper', $content(Posts::class, 'news', 'category'));
    $add('Projects', 'bi-briefcase', $content(Projects::class, 'projects', 'category'));
    $add('Services', 'bi-gear', $content(Services::class, 'services', 'summary'));

    $msgs = [];
    foreach (Messages::paginate('', $q, 1, 4)['items'] as $m) {
        $msgs[] = ['label' => $m['name'] . ($m['subject'] ? ' — ' . $m['subject'] : ''), 'sub' => $m['email'] . ($m['status'] === 'new' ? ' · unread' : ''), 'url' => admin_url('messages?id=' . $m['id'])];
    }
    $add('Messages', 'bi-chat-dots', $msgs);

    $subs = [];
    foreach (Subscribers::paginate($q, 1, 4)['items'] as $s) {
        $subs[] = ['label' => $s['email'], 'sub' => ucfirst($s['status']), 'url' => admin_url('subscribers?q=' . rawurlencode($s['email']))];
    }
    $add('Subscribers', 'bi-envelope-paper', $subs);

    if (Auth::is('admin')) {
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $users = [];
        foreach (DB::all('SELECT id, name, email FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY name LIMIT 4', [$like, $like]) as $u) {
            $users[] = ['label' => $u['name'], 'sub' => $u['email'], 'url' => admin_url('users?action=edit&id=' . $u['id'])];
        }
        $add('Users', 'bi-people', $users);
    }
}
json_response(['ok' => true, 'groups' => $groups]);