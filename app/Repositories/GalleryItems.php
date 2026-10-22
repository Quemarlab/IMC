<?php
declare(strict_types=1);

namespace YM\Repositories;

final class GalleryItems extends Repository
{
    protected static string $table = 'gallery_items';
    protected static string $folder = 'gallery';
    protected static string $order = 'sort_order ASC, id DESC';
    protected static array $searchCols = ['title', 'category'];
    protected static array $fileCols = ['image'];
    protected static bool $hasSlug = false;
}
