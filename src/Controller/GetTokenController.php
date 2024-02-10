<?php

namespace App\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetTokenController
{
    public function __invoke(): void
    {
        throw new NotFoundHttpException();
    }
}
