<?php

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class HttpUtils
{
    /**
     * Check http code to return a formatted array.
     * Status: 400, message: Bad Request.
     * Status: 403, message: Access denied.
     * Default : 404, message: Nothing found here.
     *
     * @return array<string, int|string>
     */
    public static function normalizeHttpError(int $statusCode): array
    {
        return [
            'status' => (400 !== $statusCode && 403 !== $statusCode) ? 404 : $statusCode,
            'message' => match ($statusCode) {
                400 => 'Bad Request.',
                403 => 'Access denied.',
                default => 'Nothing found here.'
            },
        ];
    }

    /**
     * @return array<int<0, max>, array<string, string|\Stringable>>
     */
    public static function normalizeConstraintsValidation(ConstraintViolationListInterface $constraintsList): array
    {
        $formattedConstraintsList = [];

        foreach ($constraintsList as $constraint) {
            $formattedConstraintsList[] = [
                'field' => $constraint->getPropertyPath(),
                'message' => $constraint->getMessage(),
            ];
        }

        return $formattedConstraintsList;
    }
}
