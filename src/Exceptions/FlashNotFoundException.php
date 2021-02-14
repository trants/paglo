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

class FlashNotFoundException extends HttpResponseException
{
    /**
     * FlashNotFoundException constructor.
     *
     * @param string $code
     * @param string $message
     */
    public function __construct($code = null, $message = null)
    {
        parent::__construct(FlashResponse::error($code, $message, null, 404));
    }
}
