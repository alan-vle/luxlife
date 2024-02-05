<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomException extends HttpException
{
    public function __construct(
        string $message,
        int $code = 400,
        \Throwable $previous = null
    ) {
        parent::__construct($code, $message, $previous);
    }
}
