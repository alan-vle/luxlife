<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User\User;
use App\Exception\CustomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @phpstan-ignore-next-line */
final class UserPasswordHasher implements ProcessorInterface
{
    /** @phpstan-ignore-next-line */
    public function __construct(
        private readonly ProcessorInterface $processor,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof User) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        if (!$data->getPlainPassword()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        if (null !== $data->getPassword() && !$this->hasher->isPasswordValid($data, $data->getOldPassword() ?: '')) {
            throw new CustomException('The old password is incorrect.', 400);
        }

        $hashedPassword = $this->hasher->hashPassword(
            $data,
            $data->getPlainPassword()
        );
        $data->setPassword($hashedPassword);
        $data->eraseCredentials();

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
