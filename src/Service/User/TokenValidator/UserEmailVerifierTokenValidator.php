<?php

namespace App\Service\User\TokenValidator;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailVerifierToken;
use App\Service\Mailer\ConfirmEmailService;
use Doctrine\ORM\EntityManagerInterface;

class UserEmailVerifierTokenValidator implements UserVerifierTokenValidatorInterface
{
    private static EntityManagerInterface $em;
    private static ConfirmEmailService $confirmEmailService;

    public function __construct(
        EntityManagerInterface $em,
        ConfirmEmailService $confirmEmailService
    ) {
        self::$em = $em;
        self::$confirmEmailService = $confirmEmailService;
    }

    public static function isAlreadyCreated(User $user): void
    {
        $emailVerifierToken = self::isExists($user->getId());

        if ($emailVerifierToken instanceof EmailVerifierToken) {
            if ($emailVerifierToken->getEmail() !== $user->getEmail()) {
                self::$em->remove($emailVerifierToken);
                self::$em->flush();
            } else {
                if (self::isStillValid($emailVerifierToken)) {
                    return;
                }
            }
        }

        self::$confirmEmailService::sendConfirmationEmail($user);
    }

    public static function isStillValid(EmailVerifierToken $emailVerifierToken): bool
    {
        if (!$emailVerifierToken->isExpired()) {
            return true;
        }

        self::$em->remove($emailVerifierToken);
        self::$em->flush();

        return false;
    }

    public static function isExists(?int $userId): ?EmailVerifierToken
    {
        return self::$em->getRepository(EmailVerifierToken::class)->findOneBy(['user' => $userId]);
    }
}
