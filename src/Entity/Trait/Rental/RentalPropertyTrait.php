<?php

namespace App\Entity\Trait\Rental;

use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalContractEnum;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\Rental\Delivery;
use App\Entity\User\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RentalPropertyTrait
{
    /**
     * Contract : LLD (1), Classic (0).
     */
    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::SMALLINT, enumType: RentalContractEnum::class)]
    private ?RentalContractEnum $contract = null;

    #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull(message: 'The kilometers should not be blank.')]
    #[ORM\Column]
    private ?int $mileageKilometers = null;

    #[ORM\Column(nullable: true)]
    private ?int $usedKilometers = null;

    #[Assert\DateTime]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fromDate = null;

    #[Assert\DateTime]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $toDate = null;

    #[Assert\Type(type: 'numeric', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Range(
        notInRangeMessage: 'There is a problem with your price.',
        min: 0,
        max: 999999.99
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $price = null;

    #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull(message: 'The kilometers should not be blank.')]
    #[ORM\Column(type: types::SMALLINT, enumType: RentalStatusEnum::class)]
    private ?RentalStatusEnum $status = null;

    /**
     * Contract : LLD (1), Classic (0).
     */
    public function getContract(): ?RentalContractEnum
    {
        return $this->contract;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?RentalStatusEnum
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
