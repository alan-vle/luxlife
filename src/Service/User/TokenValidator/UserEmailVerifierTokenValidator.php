<?php

namespace App\Service\User\TokenValidator;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailVerifierToken;
use App\Service\Mailer\ConfirmEmailService;
use Doctrine\ORM\EntityManagerInterface;

class UserEmailVerifierTokenValidator implements UserVerifierTokenValidatorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ConfirmEmailService $confirmEmailService
    ) {
    }

    //    public static function isValid(EmailVerifierToken $emailVerifierToken, User $user)
    //    {
    //
    //    }

    public function isAlreadyGenerated(User $user): void
    {
        $emailVerifierToken = self::isExists($user->getId());

        if ($emailVerifierToken instanceof EmailVerifierToken) {
            if ($emailVerifierToken->getEmail() !== $user->getEmail()) {
                $this->em->remove($emailVerifierToken);
                $this->em->flush();
            } else {
                if (self::isStillValid($emailVerifierToken)) {
                    return;
                }
            }
        }

        $this->confirmEmailService->sendConfirmationEmail($user);
    }

    public function isStillValid(EmailVerifierToken $emailVerifierToken): bool
    {
        if (!$emailVerifierToken->isExpired()) {
            return true;
        }

        $this->em->remove($emailVerifierToken);
        $this->em->flush();

        return false;
    }

    public function isExists(?int $userId): ?EmailVerifierToken
    {
        return $this->em->getRepository(EmailVerifierToken::class)->findOneBy(['user' => $userId]);
    }
}
