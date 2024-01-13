<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Car\Car;
use App\Entity\Enum\AgencyStatusEnum;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\User;
use App\Repository\AgencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgencyRepository::class)]
#[ORM\Table(name: '`agency`')]
#[UniqueEntity('email', message: 'This agency email is already used.')]
#[ApiResource(
    normalizationContext: ['groups' => ['agency:read', 'identifier']],
    denormalizationContext: ['groups' => ['agency:write', 'agency:update']],
)]
#[GetCollection]
#[Get(
    normalizationContext: ['groups' => ['agency:read', 'agency-review:read']]
)]
#[Post(
    security: "is_granted('ROLE_ADMIN')",
    validationContext: ['groups' => ['Default', 'agency:write']]
)]
#[Put(
    security: "is_granted('ROLE_ADMIN')",
    securityPostDenormalize: "is_granted('ROLE_ADMIN')"
)]
#[Patch(security: "is_granted('ROLE_ADMIN') or object.getDirector() == user")]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\HasLifecycleCallbacks]
class Agency
{
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The address should not be blank.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid address.')]
    #[Assert\Length(max: 130, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[Groups(['agency:read', 'agency:write'])]
    #[ORM\Column(length: 130)]
    private ?string $address = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The city should not be blank.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid city.')]
    #[Assert\Length(max: 50, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[Groups(['agency:read', 'agency:write'])]
    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\Regex(pattern: '/@luxlife\.com$/i', message: 'The e-mail address must belong to luxlife.')]
    #[Assert\NotBlank(message: 'The email should not be blank.')]
    #[Assert\Length(max: 180, maxMessage: 'The email cannot be longer than {{ limit }} characters.')]
    #[Groups(['agency:read', 'agency:write'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Assert\Type(type: 'object', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank]
    #[Groups(['agency:read', 'agency:write'])]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $openingHours = null;

    #[Assert\Type(type: 'object', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank]
    #[Groups(['agency:read', 'agency:write'])]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $closingHours = null;

    /**
     * @var ArrayCollection<int, User> $users
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['agency-admin:read', 'agency-director:read'])]
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: User::class)]
    private Collection $users;

    #[Groups(['agency:read', 'agency:write'])]
    #[ORM\Column(type: Types::SMALLINT, enumType: AgencyStatusEnum::class)]
    private ?AgencyStatusEnum $status = null;

    #[Groups(['agency:read'])]
    private bool $isOpen = false;

    /**
     * @var ArrayCollection<int, Car> $cars
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['agency-admin:read', 'agency-director:read'])]
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: Car::class, orphanRemoval: true)]
    private Collection $cars;

    /**
     * @var ArrayCollection<int, Review> $reviews
     */
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['agency-review:read'])]
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->cars = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getDirector(): ?User
    {
        if (0 === count($this->users)) {
            return null;
        }

        foreach ($this->users as $user) {
            if (in_array('ROLE_DIRECTOR', $user->getRoles())) {
                return $user;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getOpeningHours(): string
    {
        $openingHours = $this->openingHours instanceof \DateTimeInterface ? $this->openingHours : false;

        return $openingHours ? $openingHours->format('H:i') : (new \DateTime())->format('H:i');
    }

    public function setOpeningHours(\DateTimeInterface $openingHours): static
    {
        $this->openingHours = $openingHours;

        return $this;
    }

    public function getClosingHours(): string
    {
        $closingHours = $this->closingHours instanceof \DateTimeInterface ? $this->closingHours : false;

        return $closingHours ? $closingHours->format('H:i') : (new \DateTime())->format('H:i');
    }

    public function setClosingHours(\DateTimeInterface $closingHours): static
    {
        $this->closingHours = $closingHours;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAgency($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAgency() === $this) {
                $user->setAgency(null);
            }
        }

        return $this;
    }

    /**
     * Get status name.
     */
    public function getStatus(): ?string
    {
        if (!$this->status instanceof AgencyStatusEnum) {
            throw new \Exception('', 500);
        }
        // First letter in uppercase, the rest in lowercase and replace underscore by space
        $normalizedStatusName = fn () => ucfirst(strtolower(str_replace('_', ' ', $this->status->name)));

        return ucfirst(strtolower($normalizedStatusName()));
    }

    public function setStatus(AgencyStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIsOpen(): bool
    {
        if (AgencyStatusEnum::ACTIVE !== $this->status) {
            return $this->isOpen = false;
        }

        $currentTime = (new \DateTime())->format('H:i');

        if ($currentTime >= $this->getOpeningHours() && $currentTime < $this->getClosingHours()) {
            $this->isOpen = true;
        }

        return $this->isOpen;
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
            $car->setAgency($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getAgency() === $this) {
                $car->setAgency(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setAgency($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getAgency() === $this) {
                $review->setAgency(null);
            }
        }

        return $this;
    }
}
