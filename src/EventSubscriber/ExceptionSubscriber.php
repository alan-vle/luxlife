<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $data = [
                'status' => 404,
                'message' => 'Element not found.',
            ];
        } elseif ($exception instanceof ValidationException) {
            $constraints = $exception->getConstraintViolationList();

            $data = [
                'status' => 400,
                'constraints' => $this->constraintsErrorFormatter($constraints),
            ];
        } else {
            $data = [
                'status' => 500,
                'message' => $exception->getMessage(),
            ];
        }

        $event->setResponse(new JsonResponse($data));
    }

    /**
     * @return array<int<0, max>, array<string, string|\Stringable>>
     */
    private function constraintsErrorFormatter(ConstraintViolationListInterface $constraints): array
    {
        $formattedErrors = [];

        foreach ($constraints as $constraint) {
            $formattedErrors[] = [
                'field' => $constraint->getPropertyPath(),
                'message' => $constraint->getMessage(),
            ];
        }

        return $formattedErrors;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
