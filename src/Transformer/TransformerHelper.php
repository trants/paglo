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

namespace Trants\Paglo\Transformer;

class TransformerHelper
{
    /**
     * Filter transformer fields.
     *
     * @param array $defaultFields
     * @param array $includeFields
     * @param array $excludeFields
     * @return array
     */
    public static function filterFields($defaultFields, $includeFields, $excludeFields = [])
    {
        if (!empty(array_filter($excludeFields))) {
            $fields = array_diff($defaultFields, $excludeFields);
        } elseif (!empty(array_filter($includeFields))) {
            $fields = array_intersect($includeFields, $defaultFields);
        } else {
            $fields = $defaultFields;
        }

        if (!in_array('id', $fields)) {
            $fields[] = 'id';
        }

        return $fields;
    }
}
