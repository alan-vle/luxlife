<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\User;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: '`review`')]
#[ApiResource(
    normalizationContext: ['groups' => ['review:read', 'identifier', 'timestamp']],
    denormalizationContext: ['groups' => ['review:write']],
)]
#[GetCollection(security: "is_granted('ROLE_USER')")]
#[Get(security: "is_granted('ROLE_ADMIN') or object.getCustomer() == user or (object.getAgency() and object.getAgency().getDirector() == user)")]
#[Post(validationContext: ['groups' => ['Default', 'user:write']])]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ApiFilter(SearchFilter::class, properties: ['agency' => 'exact'])]
#[ApiFilter(NumericFilter::class, properties: ['star'])]
#[ORM\HasLifecycleCallbacks]
class Review
{
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'numeric', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Range(
        notInRangeMessage: 'There is a problem with your star.',
        min: 0,
        max: 5.0
    )]
    #[Groups(['review:read', 'review:write'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $star = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The details should not be blank.')]
    #[Groups(['review:read', 'review:write'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['review:read', 'review:write'])]
    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    #[Groups('review:read')]
    private ?string $customerFullName = null; /* @phpstan-ignore-line */

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

    public function getCustomerFullName(): string
    {
        if (null === $customer = $this->getCustomer()) {
            throw new InternalErrorException();
        }

        $fullNameExplode = explode(' ', $customer->getFullName() ?: '');
        $lastNameInitial = substr($fullNameExplode[1], 0, 1);

        return $fullNameExplode[0].' '.$lastNameInitial.'.';
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
