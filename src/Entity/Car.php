<?php

namespace App\Entity;

use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[ORM\Column]
    private ?int $kilometers = null;

    #[ORM\Column]
    private ?bool $status = null;

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

    public function __construct()
    {
        $this->problemCars = new ArrayCollection();
        $this->rentals = new ArrayCollection();
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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
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
}
