<?php

namespace App\EventSubscriber\Login;

use App\Entity\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtAuthSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $appEnv
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccessResponse',
        ];
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        if ('dev' !== $this->appEnv) {
            return;
        }

        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data = $event->getData();

        $data['user'] = [
            'uuid' => $user->getUuid(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}
