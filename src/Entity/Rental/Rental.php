<?php

namespace App\Entity\Rental;

use App\Entity\Car\Car;
use App\Entity\Trait\Rental\RentalPropertyTrait;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User;
use App\Repository\Rental\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\Table(name: '`rental`')]
#[ORM\HasLifecycleCallbacks]
class Rental implements RentalInterface
{
    use RentalPropertyTrait;
    use UuidTrait;
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'myRentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'myManagedRentals')]
    private ?User $employee = null;

    #[ORM\OneToOne(mappedBy: 'rental', cascade: ['persist', 'remove'])]
    private ?Delivery $delivery = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
