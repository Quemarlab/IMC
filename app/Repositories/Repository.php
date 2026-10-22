<?php
declare(strict_types=1);

namespace YM\Repositories;

use YM\Core\DB;
use YM\Core\Upload;

/** Shared CRUD / pagination for content tables (projects, posts, services, gallery). */
abstract class Repository
{
    protected static string $table;
    protected static string $folder;
    protected static string $order = 'id DESC';
    /** Columns searched by the admin/public search box. */
    protected static array $searchCols = ['title'];
    /** Extra SQL (no leading AND) restricting what the public site may see. */
    protected static string $publicScope = 'is_published = 1';
    /** Columns holding uploaded filenames; removed from disk on delete. */
    protected static array $fileCols = ['cover_image'];
    protected static bool $hasSlug = true;

    public static function table(): string
    {
        return static::$table;
    }

    public static function folder(): string
    {
        return static::$folder;
    }

    public static function find(int $id): ?array
    {
        return DB::one('SELECT * FROM `' . static::$table . '` WHERE id = ?', [$id]);
    }

    public static function findPublicBySlug(string $slug): ?array
    {
        return DB::one(
            'SELECT * FROM `' . static::$table . '` WHERE slug = ? AND ' . static::$publicScope,
            [$slug]
        );
    }

    /**
     * @param array{page?:int,per?:int,q?:string,public?:bool,where?:string,params?:array,order?:string} $o
     * @return array{items:array,total:int,page:int,pages:int,per:int}
     */
    public static function paginate(array $o = []): array
    {
        $per = max(1, min(100, (int) ($o['per'] ?? 12)));
        $page = max(1, (int) ($o['page'] ?? 1));
        $where = ['1=1'];
        $params = [];

        if (!empty($o['public'])) {
            $where[] = '(' . static::$publicScope . ')';
        }
        if (!empty($o['q'])) {
            $likes = [];
            foreach (static::$searchCols as $col) {
                $likes[] = "`$col` LIKE ?";
                $params[] = '%' . addcslashes($o['q'], '%_\\') . '%';
            }
            $where[] = '(' . implode(' OR ', $likes) . ')';
        }
        if (!empty($o['where'])) {
            $where[] = '(' . $o['where'] . ')';
            array_push($params, ...($o['params'] ?? []));
        }

        $whereSql = implode(' AND ', $where);
        $total = (int) DB::val('SELECT COUNT(*) FROM `' . static::$table . "` WHERE $whereSql", $params);
        $pages = max(1, (int) ceil($total / $per));
        $page = min($page, $pages);
        $order = $o['order'] ?? static::$order;

        $items = DB::all(
            'SELECT * FROM `' . static::$table . "` WHERE $whereSql ORDER BY $order LIMIT $per OFFSET " . (($page - 1) * $per),
            $params
        );
        return compact('items', 'total', 'page', 'pages', 'per');
    }

    public static function latest(int $limit = 3, string $extraWhere = ''): array
    {
        return static::paginate(['per' => $limit, 'public' => true, 'where' => $extraWhere ?: null])['items'];
    }

    public static function create(array $data): int
    {
        if (static::$hasSlug) {
            $data['slug'] = static::uniqueSlug($data['slug'] ?? $data['title']);
        }
        return DB::insert(static::$table, $data);
    }

    public static function update(int $id, array $data): void
    {
        if (static::$hasSlug && isset($data['slug'])) {
            $data['slug'] = static::uniqueSlug($data['slug'] ?: ($data['title'] ?? 'item'), $id);
        }
        DB::update(static::$table, $id, $data);
    }

    public static function delete(int $id): void
    {
        $row = static::find($id);
        if (!$row) {
            return;
        }
        foreach (static::$fileCols as $col) {
            Upload::delete(static::$folder, $row[$col] ?? null);
        }
        DB::run('DELETE FROM `' . static::$table . '` WHERE id = ?', [$id]);
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = slugify($source);
        $slug = $base;
        $i = 2;
        while (DB::val('SELECT id FROM `' . static::$table . '` WHERE slug = ? AND id <> ?', [$slug, $ignoreId ?? 0])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public static function count(bool $publicOnly = false): int
    {
        return (int) DB::val('SELECT COUNT(*) FROM `' . static::$table . '`' . ($publicOnly ? ' WHERE ' . static::$publicScope : ''));
    }

    public static function categories(): array
    {
        return array_column(DB::all(
            'SELECT DISTINCT category FROM `' . static::$table . "` WHERE category <> '' AND (" . static::$publicScope . ') ORDER BY category'
        ), 'category');
    }
}
