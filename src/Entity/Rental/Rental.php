<?php

namespace App\Entity\Rental;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Car\Car;
use App\Entity\Trait\Rental\RentalPropertyTrait;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\User;
use App\Repository\Rental\RentalRepository;
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
    security: "object.getStatus() === 'Draft' and is_granted('ROLE_ADMIN') or object.getCustomer() == user or object.getEmployee() == user or ".
    '(object.getEmployee() and object.getEmployee().getAgency() and object.getEmployee().getAgency().getDirector() == user)',
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\HasLifecycleCallbacks]
class Rental implements RentalInterface
{
    use RentalPropertyTrait;
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read', 'rental-agent:write'])]
    #[ORM\ManyToOne(inversedBy: 'myRentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read'])]
    #[ORM\ManyToOne(inversedBy: 'myManagedRentals')]
    private ?User $employee = null;

    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\OneToOne(mappedBy: 'rental', cascade: ['persist', 'remove'])]
    private ?Delivery $delivery = null;

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['rental:read', 'rental:write'])]
    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Groups(['rental:read', 'rental:write'])]
    public bool $draftRental = false;

    public bool $isFixtures = false;

    public function getId(): ?int
    {
        return $this->id;
    }
}
