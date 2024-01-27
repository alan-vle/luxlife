<?php

namespace App\Entity\Trait\Rental;

use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalContractEnum;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\Rental\Delivery;
use App\Entity\User\User;
use App\Service\Utils\EnumUtils;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait RentalPropertyTrait
{
    /**
     * Contract : LLD (1), Classic (0).
     */
    #[Assert\Type(type: 'object', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column(type: Types::SMALLINT, enumType: RentalContractEnum::class)]
    private ?RentalContractEnum $contract = null;

    #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull(message: 'The kilometers should not be blank.')]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column]
    private ?int $mileageKilometers = null;

    #[Groups(['rental:read', 'rental-agent:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $usedKilometers = null;

    #[Assert\GreaterThanOrEqual('now')]
    #[Assert\NotBlank]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fromDate = null;

    #[Assert\GreaterThanOrEqual('now')]
    #[Assert\NotBlank]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $toDate = null;

    #[Assert\Type(type: 'numeric', message: 'The value {{ value }} is not a valid {{ type }}.')]
    /*    #[Assert\NotNull]
        #[Assert\NotBlank]
        #[Assert\Range(
            notInRangeMessage: 'There is a problem with your price.',
            min: 0,
            max: 999999.99
        )]*/
    #[Groups(['rental:read'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $price = null;

    #[Groups(['rental:read'])]
    #[ORM\Column(type: types::SMALLINT, enumType: RentalStatusEnum::class)]
    private ?RentalStatusEnum $status = null;

    /**
     * Contract : Classic (0), LLD (1).
     */
    public function getContract(): ?string
    {
        return EnumUtils::nameNormalizer($this->contract);
    }

    public function setContract(RentalContractEnum $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function getMileageKilometers(): ?int
    {
        return $this->mileageKilometers;
    }

    public function setMileageKilometers(int $mileageKilometers): static
    {
        $this->mileageKilometers = $mileageKilometers;

        return $this;
    }

    public function getUsedKilometers(): ?int
    {
        return $this->usedKilometers;
    }

    public function setUsedKilometers(?int $usedKilometers): static
    {
        $this->usedKilometers = $usedKilometers;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): static
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(\DateTimeInterface $toDate): static
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getPrice(): ?int
    {
        return (int) $this->price;
    }

    #[ORM\PrePersist, ORM\PreUpdate]
    public function setPrice(PrePersistEventArgs|PreUpdateEventArgs|string $price = null): ?static
    {
        if (is_string($price)) {
            $this->price = $price;
        }
        $car = $this->getCar() instanceof Car ? $this->getCar() : new Car();

        $this->price = (string) ((int) $car->getPricePerKilometer() * (int) $this->mileageKilometers);

        return $this;
    }

    public function getStatus(): string
    {
        return EnumUtils::nameNormalizer($this->status);
    }

    public function getBrutStatus(): ?RentalStatusEnum
    {
        return $this->status;
    }

    public function setStatus(RentalStatusEnum $status): static
    {
        $this->status = $status;

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

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
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
