<?php

namespace App\Entity\Rental;

use App\Entity\Enum\Rental\DeliveryStatusEnum;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Rental\DeliveryRepository;
use App\Service\Utils\EnumUtils;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeliveryRepository::class)]
#[ORM\Table(name: '`delivery`')]
#[ORM\HasLifecycleCallbacks]
class Delivery
{
    use UuidTrait;
    use TimeStampTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column(type: types::SMALLINT, enumType: DeliveryStatusEnum::class)]
    private ?DeliveryStatusEnum $status = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The address should not be blank.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid address.')]
    #[Assert\Length(max: 130, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[ORM\Column(length: 130)]
    private ?string $address = null;

    #[Assert\DateTime]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\OneToOne(inversedBy: 'delivery', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;

    #[ORM\OneToOne(inversedBy: 'delivery', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?RentalArchived $rentalArchived = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return EnumUtils::nameNormalizer($this->status);
    }

    public function setStatus(DeliveryStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): static
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getRental(): ?Rental
    {
        return $this->rental;
    }

    public function setRental(Rental $rental): static
    {
        $this->rental = $rental;

        return $this;
    }

    public function getTrackNumber(): ?Uuid
    {
        return $this->uuid;
    }

    public function setRentalArchived(?RentalArchived $rentalArchived): void
    {
        $this->rentalArchived = $rentalArchived;
    }

    public function getRentalArchived(): ?RentalArchived
    {
        return $this->rentalArchived;
    }
}
