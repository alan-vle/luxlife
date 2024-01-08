<?php

namespace App\Entity\Car;

use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Car\ProblemCarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProblemCarRepository::class)]
#[ORM\Table(name: '`problem_car`')]
#[ORM\HasLifecycleCallbacks]
class ProblemCar
{
    use UuidTrait;
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The description should not be blank.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * Type : Failure (0), Accident (1).
     */
    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column]
    private ?bool $type = null;

    #[Assert\DateTime]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $problemDate = null;

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
     * Type : Failure (0), Accident (1).
     */
    public function getType(): ?bool
    {
        return $this->type;
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
