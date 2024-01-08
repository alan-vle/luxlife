<?php

namespace App\Entity\Car;

use App\Entity\Agency;
use App\Entity\Enum\Car\CarStatusEnum;
use App\Entity\Rental\Rental;
use App\Entity\Rental\RentalArchived;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Car\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\Table(name: '`car`')]
#[ORM\HasLifecycleCallbacks]
class Car
{
    use UuidTrait;
    use TimeStampTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The model should not be blank.')]
    #[Assert\Length(max: 50, maxMessage: 'The model cannot be longer than {{ limit }} characters')]
    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull(message: 'The kilometers should not be blank.')]
    #[ORM\Column]
    private ?int $kilometers = 0;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::SMALLINT, enumType: CarStatusEnum::class)]
    private ?CarStatusEnum $status = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manufacturer $manufacturer = null;

    /**
     * @var ArrayCollection<int, ProblemCar> $problemCars
     */
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: ProblemCar::class, orphanRemoval: true)]
    private Collection $problemCars;

    /**
     * @var ArrayCollection<int, Rental> $rentals
     */
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Rental::class)]
    private Collection $rentals;

    /**
     * @var ArrayCollection<int, RentalArchived> $rentalsArchived
     */
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: RentalArchived::class)]
    private Collection $rentalsArchived;

    public function __construct()
    {
        $this->problemCars = new ArrayCollection();
        $this->rentals = new ArrayCollection();
        $this->rentalsArchived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getKilometers(): ?int
    {
        return $this->kilometers;
    }

    public function setKilometers(int $kilometers): static
    {
        $this->kilometers = $kilometers;

        return $this;
    }

    public function isStatus(): ?CarStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CarStatusEnum $status): static
    {
        $this->status = $status;

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

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return Collection<int, ProblemCar>
     */
    public function getProblemCars(): Collection
    {
        return $this->problemCars;
    }

    public function addProblemCar(ProblemCar $problemCar): static
    {
        if (!$this->problemCars->contains($problemCar)) {
            $this->problemCars->add($problemCar);
            $problemCar->setCar($this);
        }

        return $this;
    }

    public function removeProblemCar(ProblemCar $problemCar): static
    {
        if ($this->problemCars->removeElement($problemCar)) {
            // set the owning side to null (unless already changed)
            if ($problemCar->getCar() === $this) {
                $problemCar->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setCar($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getCar() === $this) {
                $rental->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RentalArchived>
     */
    public function getRentalsArchived(): Collection
    {
        return $this->rentalsArchived;
    }

    public function addRentalsArchived(RentalArchived $rentalsArchived): static
    {
        if (!$this->rentalsArchived->contains($rentalsArchived)) {
            $this->rentalsArchived->add($rentalsArchived);
            $rentalsArchived->setCar($this);
        }

        return $this;
    }

    public function removeRentalsArchived(RentalArchived $rentalsArchived): static
    {
        if ($this->rentalsArchived->removeElement($rentalsArchived)) {
            // set the owning side to null (unless already changed)
            if ($rentalsArchived->getCar() === $this) {
                $rentalsArchived->setCar(null);
            }
        }

        return $this;
    }
}
