<?php

namespace App\Entity\Rental;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Agency;
use App\Entity\Car\Car;
use App\Entity\Trait\Rental\RentalPropertyTrait;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\User;
use App\Repository\Rental\RentalRepository;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\Table(name: '`rental`')]
#[ApiResource(
    normalizationContext: ['groups' => ['rental:read', 'identifier', 'timestamp']],
    denormalizationContext: ['groups' => ['rental:write']],
    security: "is_granted('ROLE_USER')"
)]
#[GetCollection]
#[Get(
    security: "is_granted('ROLE_ADMIN') or object.getCustomer() == user or object.getEmployee() == user or ".
    '(object.getEmployee() and object.getEmployee().getAgency() and object.getEmployee().getAgency().getDirector() == user)',
)]
#[Post(
    security: "is_granted('ROLE_USER')",
    validationContext: ['groups' => ['Default', 'rental:write']]
)]
#[Put(
    security: "is_granted('ROLE_ADMIN')",
    securityPostDenormalize: "is_granted('ROLE_ADMIN')"
)]
#[Patch(
    security: "(object.getStatus() === 'Draft' or object.getStatus() === 'Reserved') and object.getCustomer() == user or is_granted('ROLE_ADMIN') or object.getEmployee() == user or ".
    '(object.getEmployee() and object.getEmployee().getAgency() and object.getEmployee().getAgency().getDirector() == user)',
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ApiFilter(SearchFilter::class, properties: [
    'agency' => 'exact',
    'car' => 'exact',
    'status' => 'exact',
])]
#[ORM\HasLifecycleCallbacks]
class Rental extends AbstractRental
{
    use RentalPropertyTrait;
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read'])]
    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Agency $agency = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read', 'rental-agency:write'])]
    #[ORM\ManyToOne(inversedBy: 'myRentals')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $customer = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read'])]
    #[ORM\ManyToOne(inversedBy: 'myManagedRentals')]
    protected ?User $employee = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Car $car = null;

    #[ApiProperty(readableLink: false, writableLink: true)]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\OneToOne(mappedBy: 'rental', cascade: ['persist', 'remove'])]
    protected ?Delivery $delivery = null;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Groups(['rental:read', 'rental:write'])]
    public bool $draftRental = false;

    public bool $isFixtures = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\PrePersist, ORM\PreUpdate]
    public function computePrice(PrePersistEventArgs|PreUpdateEventArgs|string|null $price = null): ?static
    {
        if (is_string($price)) {
            $this->price = $price;
        }

        $car = $this->getCar() instanceof Car ? $this->getCar() : new Car();

        $this->price = (string) ((int) $car->getPricePerKilometer() * (int) $this->mileageKilometers);

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

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }
}
