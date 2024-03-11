<?php

namespace App\EntityListener;

use App\Entity\User\User;
use App\Service\User\TokenValidator\UserEmailVerifierTokenValidator;
use App\Service\User\UserUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserEntityListener
{
    public function __construct(
        private readonly UserEmailVerifierTokenValidator $emailVerifierTokenValidator,
        private readonly UserUtils $userUtils
    ) {
    }

    public function prePersist(User $user, PrePersistEventArgs $args): void
    {
        if ($user->isFixtures) {
            return;
        }

        $this->userUtils->defineRoleAccordingToCase($user);

        $this->userUtils->isAdminOrAgencyDirector($user);
    }

    public function postPersist(User $user): void
    {
        if ($user->isVerifiedEmail()) {
            return;
        }

        $this->emailVerifierTokenValidator->generate($user);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function preUpdate(User $user, PreUpdateEventArgs $args): void
    {

        if ($args->hasChangedField('email')) {
            // if email has changed, set verified email to false before updating
            // To let regeneration of email token after updating
            if ($user->isVerifiedEmail()) {
                $user->setVerifiedEmail(false);
            }
        } elseif ($args->hasChangedField('roles')) {
            $this->userUtils->updateRoleAccordingToCase($user);
        }
    }

    public function postUpdate(User $user): void
    {
        if ($user->isFixtures) {
            return;
        }

        $this->postPersist($user);
    }
}
