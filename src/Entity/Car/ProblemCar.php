<?php

namespace App\Entity\Car;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Car\ProblemCarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProblemCarRepository::class)]
#[ORM\Table(name: '`problem_car`')]
#[ApiResource(
    normalizationContext: ['groups' => ['problem-car:read', 'identifier', 'timestamp']],
    denormalizationContext: ['groups' => ['problem-car:write', 'problem-car:update']],
    security: "is_granted('ROLE_AGENT')"
)]
#[GetCollection]
#[Get]
#[Post(
    security: "is_granted('ROLE_ADMIN')",
    validationContext: ['groups' => ['Default', 'problem-car:write']]
)]
#[Put(
    security: "is_granted('ROLE_ADMIN')",
    securityPostDenormalize: "is_granted('ROLE_ADMIN')"
)]
#[Patch(
    security: "is_granted('ROLE_ADMIN')",
    securityPostDenormalize: "is_granted('ROLE_ADMIN')"
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\HasLifecycleCallbacks]
class ProblemCar
{
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The description should not be blank.')]
    #[Groups(['problem-car:read', 'problem-car:write'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * Type : Failure (0), Accident (1).
     */
    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Groups(['problem-car:read', 'problem-car:write'])]
    #[ORM\Column]
    private ?bool $type = null;

    #[Assert\NotBlank]
    #[Assert\LessThan('now')]
    #[Groups(['problem-car:read', 'problem-car:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $problemDate = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['problem-car:read', 'problem-car:write'])]
    #[ORM\ManyToOne(inversedBy: 'problemCars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Type : Failure (false|0), Accident (true|1).
     */
    public function getType(): ?string
    {
        return $this->type ? 'Accident' : 'Failure';
    }

    public function setType(bool $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getProblemDate(): ?\DateTimeInterface
    {
        return $this->problemDate;
    }

    public function setProblemDate(\DateTimeInterface $problemDate): static
    {
        $this->problemDate = $problemDate;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }
}
