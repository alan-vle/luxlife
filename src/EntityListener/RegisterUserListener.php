<?php

namespace App\EntityListener;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailAbstractVerifierToken;
use App\Service\Mailer\ConfirmEmailService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class RegisterUserListener
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ConfirmEmailService $confirmEmailService
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postPersist(User $user, PostPersistEventArgs $postPersistEventArgs): void
    {
        if ($user->isVerifiedEmail()) {
            return;
        }

        // Send the confirmation email
        $this->confirmEmailService->sendConfirmationEmail($user);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function preUpdate(User $user, PreUpdateEventArgs $preUpdateEventArgs): void
    {
        $entityChangeSet = $preUpdateEventArgs->getEntityChangeSet();

        if (!array_key_exists('email', $entityChangeSet)) {
            $this->em->getEventManager()->addEventListener(Events::preUpdate, $this);

            return;
        }

        $emailVerifierTokenExists = $this->em->getRepository(EmailAbstractVerifierToken::class)->findOneBy(['user' => $user->getId()]);

        if ($emailVerifierTokenExists) {
            $this->em->remove($emailVerifierTokenExists);
        }

        if ($user->isVerifiedEmail()) {
            $user->setVerifiedEmail(false);
        }
        $this->em->flush();
        $this->em->getEventManager()->addEventListener(Events::preUpdate, $this);

        // Send the confirmation email
        $this->confirmEmailService->sendConfirmationEmail($user);
    }
}
