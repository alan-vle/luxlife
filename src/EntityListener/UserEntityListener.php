<?php

namespace App\EntityListener;

use App\Entity\User\User;
use App\Service\User\TokenValidator\UserEmailVerifierTokenValidator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserEntityListener
{
    public function __construct(
        private readonly UserEmailVerifierTokenValidator $emailVerifierTokenValidator,
        private readonly Security $security
    ) {
    }

    public function prePersist(User $user, PrePersistEventArgs $args): void
    {
        if ($user->isFixtures()) {
            return;
        }

        $this->checkRoles($user);

        $this->checkAgencyOwner($user);
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
        if ($user->isFixtures()) {
            return;
        }

        $this->checkRoles($user);

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
        if ($user->isFixtures()) {
            return;
        }

        $this->postPersist($user);
    }

    /**
     * Check roles submitted by the user.
     */
    private function checkRoles(User $user): void
    {
        if (!$this->security->getUser()) {
            $user->setRoles(['customer']);
        } elseif ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        } elseif ($this->security->isGranted('ROLE_DIRECTOR')) {
            $user->setRoles(['agent']);
        } else {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }
    }

    private function checkAgencyOwner(User $user): void
    {
        if (!$user->getAgency()) {
            return;
        }

        if (
            $this->security->isGranted('ROLE_ADMIN')
            || (
                $this->security->isGranted('ROLE_DIRECTOR')
                && $user->getAgency()->getDirector() === $this->security->getUser()
            )
        ) {
            return;
        } else {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }
    }
}
