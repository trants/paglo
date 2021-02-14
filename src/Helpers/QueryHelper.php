<?php
/*
 * Son T. Tran
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Son T. Tran license that is
 * available through the world-wide-web at this URL:
 * https://www.trants.io/LICENSE.txt
 *
 * DISCLAIMER
 *
 * @author          Son T. Tran
 * @package         trants/paglo
 * @copyright       Copyright (c) Son T. Tran. ( https://trants.io )
 * @license         https://www.trants.io/LICENSE.txt
 *
 */

namespace Trants\Paglo\Helpers;

class QueryHelper
{
    /**
     * Showing different LIKE operators with '%' and '_' wildcards.
     *
     * @param string $string
     * @return string|string[]
     */
    public static function escapeLikeWildcard($string)
    {
        $string       = addslashes($string);
        $replacements = [
            '%' => '\%',
            '_' => '\_',
        ];
        return str_replace(array_keys($replacements), array_values($replacements), $string);
    }

    /**
     * Copies data from one table and inserts it into another table.
     *
     * @param string $model
     * @param array $columns
     * @param \Illuminate\Database\Eloquent\Builder $select
     * @return bool
     */
    public static function insertFromSelectStatement($model, $columns, $select)
    {
        $query = (new $model)->getQuery();
        $sql   = "insert into {$query->from} (" . implode(', ', $columns) . ") {$select->toSql()}";

        return $query->getConnection()->insert($sql, $select->getBindings());
    }
}
