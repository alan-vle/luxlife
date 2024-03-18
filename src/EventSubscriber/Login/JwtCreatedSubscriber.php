<?php

namespace App\EventSubscriber\Login;

use App\Entity\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JwtCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof User) {
            $payload = $event->getData();
            $payload['full_name'] = $user->getFullName();
            $payload['uuid'] = $user->getUuid();

            if (null !== $user->getAgency()) {
                $payload['agency']['uuid'] = $user->getAgency()->getUuid();
            } elseif (null !== $user->getCustomerId()) {
                $payload['customer_id'] = $user->getCustomerId();
            }

            $event->setData($payload);
        } else {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
