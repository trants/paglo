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

namespace Trants\Paglo\Model;

use Carbon\Carbon;
use Trants\Paglo\Exceptions\FlashNotFoundException;

trait FlashModelTrait
{
    /**
     * Throws an exception if the model is not found.
     *
     * @param string $id
     * @return static
     * @throws \Trants\Paglo\Exceptions\FlashNotFoundException
     */
    public static function findOrError($id)
    {
        $data = static::find($id);

        if (!$data) {
            throw new FlashNotFoundException('ResourceNotFound', 'Resource not found');
        }

        return $data;
    }

    /**
     * Get table name.
     *
     * @return static
     */
    public static function getTableName()
    {
        return (new static)->getTable();
    }

    /**
     * Extract data.
     *
     * @param string $fields
     * @return array
     */
    public function extractData($fields)
    {
        $data = array_flip($fields);

        foreach ($data as $field => $_) {
            $value = $this->{$field};

            if ($value instanceof Carbon) {
                $data[$field] = $value->toIso8601String();
            } else {
                $data[$field] = $value;
            }
        }

        return $data;
    }
}
