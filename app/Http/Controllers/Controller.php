<?php

namespace App\Http\Controllers;

use App\Contracts\Resource;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected array $response = ['success' => true, 'data' => [], 'message' => ''];
    protected int $http_status_code = Response::HTTP_OK;

    protected function setResponse(array $data = [], int $http_status_code = Response::HTTP_OK, string $message = ''): void
    {
        $this->response['data'] = $data;
        $this->response['message'] = $message;
        $this->http_status_code = $http_status_code;
    }

    protected function sendErrorResponse(string $message, int $http_status_code): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'errors' => ['message' => $message]])->setStatusCode($http_status_code);
    }

    protected function sendResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->response)->setStatusCode($this->http_status_code);
    }

    protected function sendResourceResponse(Resource $resource, Model $model): \Illuminate\Http\JsonResponse
    {
        $this->response['data'] = $resource->toArray($model, $this->response['data']);

        return response()->json($this->response)->setStatusCode($this->http_status_code);
    }
}
