<?php

namespace App\Service\User\TokenValidator;

use App\Entity\User\User;
use App\Entity\User\Verifier\EmailAbstractVerifierToken;
use Doctrine\ORM\EntityManagerInterface;

interface UserVerifierTokenValidatorInterface
{
    public function __construct(EntityManagerInterface $em);

    /**
     * Check if user token entity (VerifierEmailToken, VerifierPhoneNumberToken) has already created.
     */
    public static function isAlreadyCreated(User $user): void;

    /**
     * Check expiration date of a user (Confirm email, Confirm phone number) token entity.
     */
    public static function isStillValid(EmailAbstractVerifierToken $emailVerifierToken): bool;
}
