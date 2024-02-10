<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use App\Exception\CustomException;
use App\Service\Utils\HttpUtils;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $appEnv
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

        if ($exception instanceof CustomException) {
            $data['status'] = $exception->getStatusCode();
            $data['message'] = $exception->getMessage();
        } elseif ($exception instanceof HttpException) {
            $data = HttpUtils::normalizeHttpError($exception->getStatusCode());
        } elseif ($exception instanceof ValidationException) {
            $constraintsList = $exception->getConstraintViolationList();
            $data = HttpUtils::normalizeHttpError(400);
            $data['constraints'] = HttpUtils::normalizeConstraintsValidation($constraintsList);
        } else {
            $data['status'] = 500;
            $data['message'] = 'dev' !== $this->appEnv ? 'Something is wrong, try again later.' : $exception->getMessage();
        }

        $event->setResponse(new JsonResponse($data, $data['status']));
    }
}
