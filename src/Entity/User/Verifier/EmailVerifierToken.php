<?php

namespace App\Entity\User\Verifier;

use App\Repository\User\Verifier\EmailVerifierTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmailVerifierTokenRepository::class)]
// #[UniqueEntity('umessage: 'Link already sent.')]
#[ORM\HasLifecycleCallbacks]
final class EmailVerifierToken extends AbstractVerifierToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\NotBlank(message: 'The email should not be blank.')]
    #[Assert\Length(max: 180, maxMessage: 'The email cannot be longer than {{ limit }} characters.')]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
