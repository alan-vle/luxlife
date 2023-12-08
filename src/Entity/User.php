<?php

namespace App\Entity;

use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`web_user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidTrait;
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 130)]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    /**
     * @var array<string> $roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?bool $active = false;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Agency $agency = null;

    /**
     * @var ArrayCollection<int, Rental> $myRentals
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Rental::class)]
    private Collection $myRentals;

    /**
     * @var ArrayCollection<int, Rental> $myManagedRentals
     */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Rental::class)]
    private Collection $myManagedRentals;

    /**
     * @var ArrayCollection<int, Review> $reviews
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Review::class)]
    private Collection $reviews;

    public function __construct()
    {
        $this->myRentals = new ArrayCollection();
        $this->myManagedRentals = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getMyRentals(): Collection
    {
        return $this->myRentals;
    }

    public function addMyRental(Rental $myRental): static
    {
        if (!$this->myRentals->contains($myRental)) {
            $this->myRentals->add($myRental);
            $myRental->setCustomer($this);
        }

        return $this;
    }

    public function removeMyRental(Rental $myRental): static
    {
        if ($this->myRentals->removeElement($myRental)) {
            // set the owning side to null (unless already changed)
            if ($myRental->getCustomer() === $this) {
                $myRental->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getMyManagedRentals(): Collection
    {
        return $this->myManagedRentals;
    }

    public function addMyManagedRental(Rental $myManagedRental): static
    {
        if (!$this->myManagedRentals->contains($myManagedRental)) {
            $this->myManagedRentals->add($myManagedRental);
            $myManagedRental->setEmployee($this);
        }

        return $this;
    }

    public function removeMyManagedRental(Rental $myManagedRental): static
    {
        if ($this->myManagedRentals->removeElement($myManagedRental)) {
            // set the owning side to null (unless already changed)
            if ($myManagedRental->getEmployee() === $this) {
                $myManagedRental->setEmployee(null);
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
            $review->setCustomer($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCustomer() === $this) {
                $review->setCustomer(null);
            }
        }

        return $this;
    }
}
