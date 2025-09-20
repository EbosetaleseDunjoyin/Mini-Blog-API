<?php 

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponder
{


    /**
     * Build valid response
     * @param  string $data
     * @param  string|array|object $data
     * @param  int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($message, $data = [], $code = Response::HTTP_OK)
    {
        return response()->json(['status' => true, 'message' => $message, 'data' => $data], $code);
    }


    /**
     * Build error responses
     * @param  string|array $message
     * @param  int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $code = Response::HTTP_BAD_REQUEST)
    {
        return response()->json(['status' => false, 'message' => $message, 'code' => $code], $code);
    }

}