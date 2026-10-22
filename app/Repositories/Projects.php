<?php
declare(strict_types=1);

namespace YM\Repositories;

final class Projects extends Repository
{
    protected static string $table = 'projects';
    protected static string $folder = 'projects';
    protected static string $order = 'is_featured DESC, completed_on DESC, id DESC';
    protected static array $searchCols = ['title', 'summary', 'category', 'client', 'location'];
}
