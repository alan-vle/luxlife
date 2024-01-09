<?php

namespace App\Entity;

use App\Entity\Car\Car;
use App\Entity\Enum\AgencyStatusEnum;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\AgencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgencyRepository::class)]
#[ORM\Table(name: '`agency`')]
#[UniqueEntity('email', message: 'This email is already in used.')]
#[ORM\HasLifecycleCallbacks]
class Agency
{
    use UuidTrait;
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The address should not be blank.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid address.')]
    #[Assert\Length(max: 130, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[ORM\Column(length: 130)]
    private ?string $address = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The city should not be blank.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid city.')]
    #[Assert\Length(max: 50, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\NotBlank(message: 'The email should not be blank.')]
    #[Assert\Length(max: 180, maxMessage: 'The email cannot be longer than {{ limit }} characters.')]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[Assert\Time, Assert\NotBlank]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $openingHours = null;

    #[Assert\Time, Assert\NotBlank]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $closingHours = null;

    /**
     * @var ArrayCollection<int, User> $users
     */
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: User::class)]
    private Collection $users;

    #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::SMALLINT, enumType: AgencyStatusEnum::class)]
    private ?AgencyStatusEnum $status = null;

    /**
     * @var ArrayCollection<int, Car> $cars
     */
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: Car::class, orphanRemoval: true)]
    private Collection $cars;

    /**
     * @var ArrayCollection<int, Review> $reviews
     */
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

    public function getOpeningHours(): ?\DateTimeInterface
    {
        return $this->openingHours;
    }

    public function setOpeningHours(\DateTimeInterface $openingHours): static
    {
        $this->openingHours = $openingHours;

        return $this;
    }

    public function getClosingHours(): ?\DateTimeInterface
    {
        return $this->closingHours;
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

    public function getStatus(): ?AgencyStatusEnum
    {
        return $this->status;
    }

    public function setStatus(AgencyStatusEnum $status): static
    {
        $this->status = $status;

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
