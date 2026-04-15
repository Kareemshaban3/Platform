<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'status'      => true,
            'status_code' => $status, 
            'message'     => $message,
            'data'        => $data,
        ], $status);
    }

    protected function errorResponse(string $message = '', int $status = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status'      => false,
            'status_code' => $status,
            'message'     => $message,
            'errors'      => $errors,
        ], $status); 
    }
}
