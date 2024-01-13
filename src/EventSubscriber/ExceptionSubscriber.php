<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire('%app_env%')] private readonly string $appEnv
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $data = $this->normalizeHttpError($exception);
        } elseif ($exception instanceof ValidationException) {
            $constraintsList = $exception->getConstraintViolationList();

            $data = [
                'status' => 400,
                'constraints' => $this->normalizeConstraintsValidation($constraintsList),
            ];
        } else {
            $data = [
                'status' => 500,
                'message' => 'dev' !== $this->appEnv ? 'Something is wrong, try again later.' : $exception->getMessage(),
            ];
        }

        $event->setResponse(new JsonResponse($data));
    }

    /**
     * Check http code to return a formatted array.
     * Status: 400, message: Bad Request.
     * Status: 403, message: Access denied.
     * Default : 404, message: Nothing found here.
     *
     * @return array<string, int|string>
     */
    private function normalizeHttpError(HttpException $exception): array
    {
        $statusCode =
            function () use ($exception): int {
                $statusCode = $exception->getStatusCode();

                if (400 === $statusCode || 403 === $statusCode) {
                    return $statusCode;
                }

                return 404;
            }
        ;

        return [
            'status' => $statusCode(),
            'message' => match ($statusCode()) {
                400 => 'Bad Request.',
                403 => 'Access denied.',
                default => 'Nothing found here.'
            },
        ];
    }

    /**
     * @return array<int<0, max>, array<string, string|\Stringable>>
     */
    private function normalizeConstraintsValidation(ConstraintViolationListInterface $constraintsList): array
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
