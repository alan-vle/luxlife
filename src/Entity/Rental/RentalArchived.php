<?php

namespace App\Entity\Rental;

use App\Entity\Car\Car;
use App\Entity\Enum\Rental\RentalContractEnum;
use App\Entity\Trait\Rental\RentalPropertyTrait;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\User\User;
use App\Repository\Rental\RentalArchivedRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RentalArchivedRepository::class)]
#[ORM\HasLifecycleCallbacks]
class RentalArchived implements RentalInterface
{
    use TimeStampTrait;
    use RentalPropertyTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Uuid(versions: [4])]
    #[Column(type: 'uuid')]
    protected ?Uuid $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'rentalsArchivedAsCustomer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\ManyToOne(inversedBy: 'rentalsArchivedAsEmployee')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\ManyToOne(inversedBy: 'rentalsArchived')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[ORM\OneToOne(mappedBy: 'rentalArchived', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Delivery $delivery = null;

    public static function convertToRentalArchived(Rental $rental): RentalArchived
    {
        $rentalArchived = new RentalArchived();
        /* @phpstan-ignore-next-line */
        $rentalArchived
            ->setCustomer($rental->getCustomer())
            ->setEmployee($rental->getEmployee())
            ->setCar($rental->getCar())
            ->setDelivery($rental->getDelivery() ?: null)
            ->setContract('Lld' === $rental->getContract() ? RentalContractEnum::LLD : RentalContractEnum::CLASSIC)
            ->setMileageKilometers($rental->getMileageKilometers() ?: 0)
            ->setUsedKilometers($rental->getUsedKilometers())
            ->setFromDate($rental->getFromDate() ?: throw new \Exception())
            ->setToDate($rental->getToDate() ?: throw new \Exception())
            ->setPrice($rental->getPrice() ? (string) $rental->getPrice() : throw new \Exception())
            ->setStatus($rental->getBrutStatus())
            ->setUuid($rental->getUuid() ?: throw new \Exception())
        ;

        return $rentalArchived;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDelivery(?Delivery $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }
}
