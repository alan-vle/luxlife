<?php

namespace App\Entity\Rental;

use App\Entity\Enum\Rental\RentalContractEnum;
use App\Entity\Enum\Rental\RentalStatusEnum;
use App\Utils\EnumUtils;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractRental implements RentalInterface
{
    /**
     * Contract : LLD (1), Classic (0).
     */
    #[Assert\Type(type: 'object', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column(type: Types::SMALLINT, enumType: RentalContractEnum::class)]
    protected ?RentalContractEnum $contract = null;

    #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull(message: 'The kilometers should not be blank.')]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column]
    protected ?int $mileageKilometers = null;

    #[Groups(['rental:read', 'rental-agency:write'])]
    #[ORM\Column(nullable: true)]
    protected ?int $usedKilometers = null;

    #[Assert\GreaterThanOrEqual('now')]
    #[Assert\NotBlank]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $fromDate = null;

    #[Assert\GreaterThanOrEqual('now')]
    #[Assert\NotBlank]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $toDate = null;

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
    protected ?string $price = null;

    #[Groups(['rental:read', 'rental-agency:write'])]
    #[ORM\Column(type: Types::SMALLINT, enumType: RentalStatusEnum::class)]
    protected ?RentalStatusEnum $status = null;

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

    public function setPrice(?string $price = null): ?static
    {
        $this->price = $price;

        return $this;
    }
}
