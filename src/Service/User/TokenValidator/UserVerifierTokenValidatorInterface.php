<?php

namespace App\Service\User\TokenValidator;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailVerifierToken;
use App\Service\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;

interface UserVerifierTokenValidatorInterface
{
    public function __construct(EntityManagerInterface $em, MailerService $confirmEmailService);

    /**
     * Check if user token entity (VerifierEmailToken, VerifierPhoneNumberToken) has already created.
     */
    public function isAlreadyGenerated(User $user): void;

    /**
     * Check expiration date of a user (Confirm email, Confirm phone number) token entity.
     */
    public function isStillValid(EmailVerifierToken $emailVerifierToken): bool;

    public function isExists(?int $userId): ?EmailVerifierToken;
}
