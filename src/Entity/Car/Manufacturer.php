<?php

namespace App\Entity\Car;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Car\ManufacturerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ManufacturerRepository::class)]
#[ORM\Table(name: '`manufacturer`')]
#[ApiResource(
    normalizationContext: ['groups' => ['manufacturer:read', 'identifier', 'timestamp']],
    denormalizationContext: ['groups' => ['manufacturer:write', 'manufacturer:update']],
)]
#[GetCollection]
#[Get(
    //    normalizationContext: ['groups' => ['manufacturer:read', 'manufacturer-admin:read']]
)]
#[Post(
    security: "is_granted('ROLE_ADMIN')",
    validationContext: ['groups' => ['Default', 'manufacturer:write']]
)]
#[Put(
    security: "is_granted('ROLE_ADMIN')",
    securityPostDenormalize: "is_granted('ROLE_ADMIN')"
)]
#[Patch(security: "is_granted('ROLE_ADMIN')")]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\HasLifecycleCallbacks]
class Manufacturer
{
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The description should not be blank.')]
    #[Assert\Length(max: 16, maxMessage: 'The name cannot be longer than {{ limit }} characters')]
    #[Groups(['manufacturer:read', 'manufacturer:write'])]
    #[ORM\Column(length: 16)]
    private ?string $name = null;

    /**
     * @var ArrayCollection<int, Car> $cars
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['manufacturer:read'])]
    #[ORM\OneToMany(mappedBy: 'manufacturer', targetEntity: Car::class, orphanRemoval: true)]
    private Collection $cars;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return ucfirst($this->name ?: '');
    }

    public function setName(string $name): static
    {
        $this->name = strtolower($name);

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setManufacturer($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getManufacturer() === $this) {
                $car->setManufacturer(null);
            }
        }

        return $this;
    }
}
