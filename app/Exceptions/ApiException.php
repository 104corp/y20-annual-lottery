<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiException extends HttpException
{
    /**
     * @var string 自定義 error message
     */
    public $errorMessage;

    /**
     * @var object|array
     */
    public $errorDetails = [];

    /**
     * @param object|array $data
     */
    public function setErrorDetails($data)
    {
        $this->errorDetails = $data;
    }
}
