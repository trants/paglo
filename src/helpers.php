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

if (!function_exists('flash_data')) {
    /**
     * Formats data as JSON response.
     *
     * @param mixed $data
     * @param \Trants\EasyJsonApi\ExtendedTransformer $transformer
     * @param array $includes
     * @param string $name
     * @param int $status
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function flash_data($data, $transformer, $includes = [], $name = null, $status = 200, array $headers = array())
    {
        return \Trants\Paglo\Response\FlashResponse::data($data, $transformer, $includes, $name, $status, $headers);
    }
}

if (!function_exists('flash_response')) {
    /**
     * Formats the array to JSON data response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function flash_response($data, $status = 200, array $headers = array())
    {
        return \Trants\Paglo\Response\FlashResponse::arrayToJSONAPIFormat($data, $status, $headers);
    }
}

if (!function_exists('flash_success')) {
    /**
     * Formats success as JSON response.
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function flash_success($message = null)
    {
        return \Trants\Paglo\Response\FlashResponse::success($message);
    }
}
