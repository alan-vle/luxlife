<?php

namespace App\Entity\Rental;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Enum\Rental\DeliveryStatusEnum;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Rental\DeliveryRepository;
use App\Service\Utils\EnumUtils;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeliveryRepository::class)]
#[ORM\Table(name: '`delivery`')]
#[ApiResource(
    normalizationContext: ['groups' => ['delivery:read', 'identifier', 'timestamp']],
)]
#[GetCollection(security: "is_granted('ROLE_ADMIN')")]
#[Get(
    security: "is_granted('ROLE_ADMIN') or ".
    '(object.getRental() and object.getRental().getEmployee().getAgency().getDirector() == user or '.
    'object.getRental().getEmployee() == user or object.getRental().getCustomer() == user)'
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\HasLifecycleCallbacks]
class Delivery
{
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Groups(['delivery:read'])]
    #[ORM\Column(type: types::SMALLINT, enumType: DeliveryStatusEnum::class)]
    private ?DeliveryStatusEnum $status = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The address should not be blank.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid address.')]
    #[Assert\Length(max: 130, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[Groups(['delivery:read', 'rental:write'])]
    #[ORM\Column(length: 130)]
    private ?string $address = null;

    #[Assert\DateTime]
    #[Assert\NotBlank]
    #[Groups(['delivery:read', 'rental:write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['delivery:read'])]
    #[ORM\OneToOne(inversedBy: 'delivery')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\OneToOne(inversedBy: 'delivery')]
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
