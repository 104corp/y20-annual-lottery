<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ApiException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ApiException) {
            return $this->getApiResponse($exception);
        }
        return $this->getUnknownHttpResponse($exception);
    }

    private function getApiResponse($exception)
    {
        $data = [
            'status' => $exception->getStatusCode(),
            'message' => $exception->errorMessage,
            'detail' => $exception->errorDetails
        ];

        return response($data, $exception->getStatusCode());
    }

    public function getUnknownHttpresponse($exception)
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $data = [
            'status' => $statusCode,
            'message' => $exception->getMessage(),
            'detail' => $exception->errorDetails ?? []
        ];

        return response($data, $statusCode);
    }
}
