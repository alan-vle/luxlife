<?php

namespace App\EntityListener;

use App\Entity\User\User;
use App\Service\User\TokenValidator\UserEmailTokenValidator;
use App\Utils\UserUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
class UserEntityListener
{
    public function __construct(
        private readonly UserEmailTokenValidator $emailTokenValidator,
        private readonly UserUtils $userUtils,
    ) {
    }

    public function prePersist(User $user): void
    {
        if (null === $user->getAgency() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $randomCustomerId = $this->userUtils->customerIdGenerator();

            $user->setCustomerId($randomCustomerId);
        }

        if ($user->isFixtures) {
            return;
        }

        $this->userUtils->defineRoleAccordingToCase($user);

        $this->userUtils->isAdminOrAgencyDirector($user);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postPersist(User $user): void
    {
        if ($user->isVerifiedEmail()) {
            return;
        }

        $this->emailTokenValidator->generate($user);
    }


    public function preUpdate(User $user, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('email')) {
            // if email has changed, set verified email to false before updating
            // To let regeneration of email token after updating
            if ($user->isVerifiedEmail()) {
                $user->setVerifiedEmail(false);
            }
        }

        if ($args->hasChangedField('roles')) {
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
