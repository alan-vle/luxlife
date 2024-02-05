<?php

namespace App\Entity\Rental;

use App\Entity\Agency;
use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalContractEnum;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Entity\User\User;

interface RentalInterface
{
    public function getAgency(): ?Agency;

    public function setAgency(Agency $agency): static;

    public function getContract(): ?string;

    public function setContract(RentalContractEnum $contract): static;

    public function getMileageKilometers(): ?int;

    public function setMileageKilometers(int $mileageKilometers): static;

    public function getUsedKilometers(): ?int;

    public function setUsedKilometers(?int $usedKilometers): static;

    public function getFromDate(): ?\DateTimeInterface;

    public function setFromDate(\DateTimeInterface $fromDate): static;

    public function getToDate(): ?\DateTimeInterface;

    public function setToDate(\DateTimeInterface $toDate): static;

    public function getPrice(): ?int;

    public function setPrice(string $price = null): ?static;

    public function getStatus(): ?string;

    public function setStatus(RentalStatusEnum $status): static;

    public function getCustomer(): ?User;

    public function setCustomer(?User $customer): static;

    public function getEmployee(): ?User;

    public function setEmployee(?User $employee): static;

    public function getDelivery(): ?Delivery;

    //    public function setDelivery(Delivery $delivery): static;

    public function getCar(): ?Car;

    public function setCar(?Car $car): static;
}
