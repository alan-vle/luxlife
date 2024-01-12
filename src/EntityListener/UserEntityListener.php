<?php

namespace App\EntityListener;

use App\Entity\User\User;
use App\Service\User\TokenValidator\UserEmailVerifierTokenValidator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserEntityListener
{
    public function __construct(
        private readonly UserEmailVerifierTokenValidator $emailVerifierTokenValidator
    ) {
    }

    public function postPersist(User $user): void
    {
        if ($user->isVerifiedEmail()) {
            return;
        }

        $this->emailVerifierTokenValidator::isAlreadyCreated($user);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function preUpdate(User $user, PreUpdateEventArgs $preUpdateEventArgs): void
    {
        // Get field changed of user entity
        $entityChangeSet = $preUpdateEventArgs->getEntityChangeSet();
        if (!array_key_exists('email', $entityChangeSet)) {
            return;
        }

        if ($user->isVerifiedEmail()) {
            $user->setVerifiedEmail(false);
        }
    }

    public function postUpdate(User $user): void
    {
        $this->postPersist($user);
    }
}
