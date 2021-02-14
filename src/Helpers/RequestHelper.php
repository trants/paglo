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

class RequestHelper
{
    /**
     * Parse the query parameters with the given options.
     *
     * @param string $value
     * @param array $acceptances
     * @return array
     */
    public static function parseSort($value, $acceptances)
    {
        $result = [];
        $sorts  = explode(',', $value);
        foreach ($sorts as $sort) {
            if (starts_with($sort, '-')) {
                $field     = substr($sort, 1);
                $direction = 'desc';
            } else {
                $field     = $sort;
                $direction = 'asc';
            }

            if (in_array($field, $acceptances)) {
                $result[$field] = $direction;
            }

        }

        return $result;
    }
}
