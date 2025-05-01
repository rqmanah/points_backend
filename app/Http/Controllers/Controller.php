<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected $service;
    protected $UpdateRequest;
    protected $StoreRequest;
    protected function sendResponse($result, $message): JsonResponse
    {
        if ($result && is_object($result) && property_exists($result, 'data')) {
            if (!is_array($result->data)) {
                $result->data = [$result->data];
            }
        } elseif ($result && is_array($result) && !isset($result['data'])) {
            $result = ['data' => $result];
        }elseif ($result && !property_exists($result, 'data') && !is_array($result)){
            $result = ['data' => $result];
        }

        $response = [
            'status' => 'success',
            'result' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    protected function sendError($error, $errorMessages = [], $code = 200): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $error,
            'result' => null
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

}
