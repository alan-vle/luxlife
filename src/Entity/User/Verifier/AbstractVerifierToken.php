<?php

namespace App\Entity\User\Verifier;

use App\Entity\Trait\UuidTrait;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractVerifierToken
{
    use UuidTrait;
    #[ORM\OneToOne(targetEntity: User::class, )]
    #[ORM\JoinColumn(unique: true, nullable: false)]
    protected ?User $user = null;

    #[ORM\Column]
    protected ?\DateTimeImmutable $expiresAt = null;

    abstract public function getId(): ?int;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(string $expiresAt): void
    {
        $this->expiresAt = new \DateTimeImmutable($expiresAt);
    }

    public function isExpired(): bool
    {
        return (new \DateTime()) > $this->expiresAt;
    }
}
