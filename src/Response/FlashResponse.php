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

namespace Trants\Paglo\Response;

use \Trants\EasyJsonApi\Serializer\JsonApiDataResponse;

use Illuminate\Http\JsonResponse;

class FlashResponse
{
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
    public static function data($data, $transformer, $includes = [], $name = null, $status = 200, array $headers = array())
    {
        $response = new JsonApiDataResponse($data, $transformer, $includes, $name);

        return $response->response($status, $headers);
    }

    /**
     * Formats errors as JSON response.
     *
     * @param string $code
     * @param string $message
     * @param string $validator
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($code = null, $message = null, $validator = null, $status = 400, $headers = [])
    {
        $data = [];
        if ($code) {
            $data['code'] = $code;
        }

        if ($message) {
            $data['message'] = $message;
        }

        if ($validator) {
            $fields = [];
            foreach ($validator->getMessageBag()->getMessages() as $field => $messages) {
                array_push($fields, [
                    'name'   => $field,
                    'errors' => $messages,
                ]);
            }

            $data['fields'] = $fields;
        }

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Formats the array to JSON data response.
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function arrayToJSONAPIFormat($data, $status = 200, array $headers = array())
    {
        $res = [
            'data' => [
                'type'       => null,
                'id'         => null,
                'attributes' => $data
            ]
        ];

        return response()->json($res, $status, $headers);
    }

    /**
     * Formats success as JSON response.
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($message = null, $headers = [])
    {
        $data = [];

        if ($message) {
            $data['message'] = $message;
        }

        return new JsonResponse((object)$data, 200, $headers);
    }
}
