<?php
declare(strict_types=1);

namespace YM\Repositories;

final class Services extends Repository
{
    protected static string $table = 'services';
    protected static string $folder = 'services';
    protected static string $order = 'sort_order ASC, id ASC';
    protected static array $searchCols = ['title', 'summary'];
}
