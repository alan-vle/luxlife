<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpUtils
{
    public static function throw400HTTPError(): void
    {
        throw new HttpException(Response::HTTP_BAD_REQUEST);
    }
}
