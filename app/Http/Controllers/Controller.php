<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Format and return error response
     *
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code = 400, $errorData = [])
    {
        return response()->json([
            'status' => 'error',
            'error' => $message,
            'error_data' => $errorData
        ], $code);
    }

    /**
     * Format and return success response
     *
     * @param  string  $message
     * @param  array|string  $data
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = '', $message = '', $code = 200)
    {
        $response = ['status' => 'success'];

        if ($message !== '') {
            $response['message'] = $message;
        }

        if ($data !== '') {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
