<?php

namespace App\traits;

trait apiResponses
{
    protected function ok($message, $data = [])
    {
        return $this->success($message, $data);
    }

    protected function success($message, $data = [], $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'status' => $statusCode
        ], $statusCode);
    }

    protected function error($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
