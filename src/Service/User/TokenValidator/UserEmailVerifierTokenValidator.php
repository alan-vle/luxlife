<?php

namespace App\Service\User\TokenValidator;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailAbstractVerifierToken;
use App\Service\Mailer\ConfirmEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserEmailVerifierTokenValidator implements UserVerifierTokenValidatorInterface
{
    private static EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        self::$em = $em;
    }

    public static function isAlreadyCreated(User $user): void
    {
        $emailVerifierTokenExists = self::$em->getRepository(EmailAbstractVerifierToken::class)->findOneBy(['user' => $user->getId()]);

        if (!$emailVerifierTokenExists) {
            return;
        }

        if (!$emailVerifierTokenExists instanceof EmailAbstractVerifierToken) {
            throw new \HttpException(Response::HTTP_NOT_FOUND);
        }

        if (self::isStillValid($emailVerifierTokenExists)) {
            return;
        }

        ConfirmEmailService::sendConfirmationEmail($user);
    }

    public static function isStillValid(EmailAbstractVerifierToken $emailVerifierToken): bool
    {
        if (!$emailVerifierToken->isExpired()) {
            return true;
        }

        self::$em->remove($emailVerifierToken);
        self::$em->flush();

        return false;
    }
}
