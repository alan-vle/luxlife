<?php

namespace App\Entity;

use App\Entity\Trait\TimeStampTrait;
use App\Entity\User\User;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: '`review`')]
#[ORM\HasLifecycleCallbacks]
class Review
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'numeric', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Range(
        notInRangeMessage: 'There is a problem with your star.',
        min: 0,
        max: 5.0
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $star = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The details should not be blank.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStar(): ?string
    {
        return $this->star;
    }

    public function setStar(string $star): static
    {
        $this->star = $star;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): static
    {
        $this->agency = $agency;

        return $this;
    }
}
