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

namespace Trants\Paglo\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Trants\Paglo\Response\FlashResponse;

class FlashErrorException extends HttpResponseException
{
    /**
     * FlashErrorException constructor.
     *
     * @param string $code
     * @param string $message
     * @param \Illuminate\Validation\Validator $validator
     */
    public function __construct($code = null, $message = null, $validator = null)
    {
        parent::__construct(FlashResponse::error($code, $message, $validator, 400));
    }

    /**
     * Add custom data to response.
     *
     * @param array $data
     * @return \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function with($data = [])
    {
        $response = $this->getResponse();
        $body     = $response->getData();

        if (isset($body->data)) {
            $data = array_merge($body->data, $data);
        }

        $body->data = $data;
        $response->setData($body);

        return new parent($response);
    }
}
