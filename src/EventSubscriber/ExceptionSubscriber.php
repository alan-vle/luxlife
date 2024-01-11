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
            $statusCode = 404 === $exception->getStatusCode();
            $data = [
                'status' => $statusCode ? 404 : 400,
                'message' => $statusCode ? 'Nothing found here.' : 'Bad request.',
            ];
        } elseif ($exception instanceof ValidationException) {
            $constraintsList = $exception->getConstraintViolationList();

            $data = [
                'status' => 400,
                'constraints' => $this->constraintsErrorFormatter($constraintsList),
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
     * @return array<int<0, max>, array<string, string|\Stringable>>
     */
    private function constraintsErrorFormatter(ConstraintViolationListInterface $constraintsList): array
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
