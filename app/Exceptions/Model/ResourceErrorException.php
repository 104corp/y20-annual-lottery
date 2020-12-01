<?php

namespace App\Exceptions\Model;

use App\Exceptions\ApiException;
use Throwable;

class ResourceErrorException extends ApiException
{
    public function __construct(
        $errorMessage = '資源取得產生問題',
        $errorCode = 400,
        Throwable $previous = null
    ) {
        parent::__construct(400, $errorMessage, $previous);

        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }
}
