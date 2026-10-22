<?php
declare(strict_types=1);

use YM\Repositories\GalleryItems;
use YM\Repositories\Posts;
use YM\Repositories\Projects;
use YM\Repositories\Services;

/**
 * Declarative definition of every content type managed by resource.php.
 * Field: [name, label, type, options]. Types: text, textarea, richtext, image, checkbox, date, datetime, number.
 * Options: rules (Validator string), side (render in sidebar), help, maxlength, rows, pattern, default, required_on_create.
 */
function resources(): array
{
    $status = static fn(array $r): string => status_badge(
        !(int) $r['is_published'] ? 'draft' : ((isset($r['published_at']) && $r['published_at'] && strtotime($r['published_at']) > time()) ? 'scheduled' : 'published')
    );

    return [
        'projects' => [
            'label' => 'Projects', 'singular' => 'project', 'icon' => 'bi-briefcase', 'nav' => 'projects',
            'repo' => Projects::class, 'image' => 'cover_image',
            'public' => fn($r) => url('projects/' . $r['slug']),
            'columns' => [
                'Category' => fn($r) => e($r['category'] ?: '—'),
                'Location' => fn($r) => e($r['location'] ?: '—'),
                'Status' => $status,
                'Updated' => fn($r) => e(time_ago($r['updated_at'])),
            ],
            'fields' => [
                ['title', 'Title', 'text', ['rules' => 'required|max:160']],
                ['summary', 'Short summary', 'textarea', ['rules' => 'max:300', 'rows' => 2, 'maxlength' => 300, 'help' => 'Shown on project cards and in search results.']],
                ['body', 'Project overview', 'richtext', []],
                ['slug', 'URL slug', 'text', ['rules' => 'max:160', 'help' => 'Leave blank to generate from the title.']],
                ['cover_image', 'Cover image', 'image', ['side' => true]],
                ['category', 'Category', 'text', ['side' => true, 'rules' => 'max:80', 'help' => 'e.g. OHS, Environment, Training']],
                ['client', 'Client', 'text', ['side' => true, 'rules' => 'max:160']],
                ['location', 'Location', 'text', ['side' => true, 'rules' => 'max:120']],
                ['completed_on', 'Completed on', 'date', ['side' => true, 'rules' => 'date']],
                ['is_featured', 'Feature on top of the list', 'checkbox', ['side' => true]],
                ['is_published', 'Published', 'checkbox', ['side' => true, 'default' => 1]],
            ],
        ],
        'news' => [
            'label' => 'News', 'singular' => 'article', 'icon' => 'bi-newspaper', 'nav' => 'news',
            'repo' => Posts::class, 'image' => 'cover_image',
            'public' => fn($r) => url('news/' . $r['slug']),
            'columns' => [
                'Category' => fn($r) => e($r['category']),
                'Status' => $status,
                'Published' => fn($r) => e(fmt_date($r['published_at'] ?: $r['created_at'])),
            ],
            'fields' => [
                ['title', 'Title', 'text', ['rules' => 'required|max:190']],
                ['excerpt', 'Excerpt', 'textarea', ['rules' => 'max:300', 'rows' => 2, 'maxlength' => 300, 'help' => 'One or two sentences for cards and Google. Generated from the text when blank.']],
                ['body', 'Article', 'richtext', ['rules' => 'required']],
                ['slug', 'URL slug', 'text', ['rules' => 'max:190', 'help' => 'Leave blank to generate from the title.']],
                ['cover_image', 'Cover image', 'image', ['side' => true]],
                ['category', 'Category', 'text', ['side' => true, 'rules' => 'required|max:80', 'default' => 'News']],
                ['published_at', 'Publish date', 'datetime', ['side' => true, 'rules' => 'date', 'help' => 'A future date schedules the article.']],
                ['is_published', 'Published', 'checkbox', ['side' => true, 'default' => 1]],
            ],
        ],
        'services' => [
            'label' => 'Services', 'singular' => 'service', 'icon' => 'bi-gear', 'nav' => 'services',
            'repo' => Services::class, 'image' => 'cover_image',
            'public' => fn($r) => url('services/' . $r['slug']),
            'columns' => [
                'Icon' => fn($r) => '<i class="bi ' . e($r['icon']) . ' fs-5"></i>',
                'Order' => fn($r) => (int) $r['sort_order'],
                'Status' => $status,
            ],
            'fields' => [
                ['title', 'Title', 'text', ['rules' => 'required|max:160']],
                ['summary', 'Short summary', 'textarea', ['rules' => 'required|max:300', 'rows' => 2, 'maxlength' => 300]],
                ['body', 'Full description', 'richtext', []],
                ['slug', 'URL slug', 'text', ['rules' => 'max:160', 'help' => 'Leave blank to generate from the title.']],
                ['icon', 'Icon', 'text', ['side' => true, 'rules' => 'required|max:60', 'pattern' => '/^bi-[a-z0-9-]+$/', 'default' => 'bi-gear', 'help' => 'A Bootstrap Icons class, e.g. bi-shield-check (see icons.getbootstrap.com).']],
                ['cover_image', 'Cover image', 'image', ['side' => true]],
                ['sort_order', 'Display order', 'number', ['side' => true, 'default' => 0, 'help' => 'Lower numbers appear first.']],
                ['is_published', 'Published', 'checkbox', ['side' => true, 'default' => 1]],
            ],
        ],
        'gallery' => [
            'label' => 'Gallery', 'singular' => 'photo', 'icon' => 'bi-images', 'nav' => 'gallery',
            'repo' => GalleryItems::class, 'image' => 'image',
            'public' => fn($r) => url('gallery'),
            'columns' => [
                'Category' => fn($r) => e($r['category'] ?: '—'),
                'Order' => fn($r) => (int) $r['sort_order'],
                'Status' => fn($r) => status_badge((int) $r['is_published'] ? 'published' : 'draft'),
            ],
            'fields' => [
                ['image', 'Photo', 'image', ['required_on_create' => true]],
                ['title', 'Caption', 'text', ['rules' => 'max:160']],
                ['category', 'Category', 'text', ['rules' => 'max:80', 'help' => 'Used for the filter buttons on the gallery page.']],
                ['sort_order', 'Display order', 'number', ['default' => 0]],
                ['is_published', 'Published', 'checkbox', ['default' => 1]],
            ],
        ],
    ];
}
