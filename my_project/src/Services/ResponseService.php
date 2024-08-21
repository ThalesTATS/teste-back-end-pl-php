<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseService
{

    public static function data($data){
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
        ], JsonResponse::HTTP_OK);
    }

    public static function success($message, $code=JsonResponse::HTTP_OK, $data=null){
        return new JsonResponse([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($message, $code=JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $errors=null){
        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
