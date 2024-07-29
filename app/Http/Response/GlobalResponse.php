<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class GlobalResponse
{
    public static function jsonResponse($data, int $code = 200, string $status = 'success', string $message = ''): JsonResponse
    {
        $response = [
            'data' => $data,
            'code' => $code,
            'status' => $status,
            'message' => $message
        ];

        return response()->json($response, $code);
    }
}
