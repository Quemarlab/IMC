<?php
declare(strict_types=1);

namespace YM\Repositories;

use YM\Core\DB;

final class Posts extends Repository
{
    protected static string $table = 'posts';
    protected static string $folder = 'news';
    protected static string $order = 'COALESCE(published_at, created_at) DESC, id DESC';
    protected static array $searchCols = ['title', 'excerpt', 'body', 'category'];
    /** Scheduled posts stay hidden until their publish date. */
    protected static string $publicScope = 'is_published = 1 AND (published_at IS NULL OR published_at <= NOW())';

    public static function related(int $id, string $category, int $limit = 3): array
    {
        return DB::all(
            'SELECT * FROM posts WHERE id <> ? AND ' . static::$publicScope . ' ORDER BY (category = ?) DESC, COALESCE(published_at, created_at) DESC LIMIT ' . (int) $limit,
            [$id, $category]
        );
    }
}
