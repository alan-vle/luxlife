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
use App\Entity\Rental\Rental;
use App\Entity\Rental\RentalArchived;
use App\Entity\Trait\TimeStampTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`app_user`')]
#[UniqueEntity('email', message: 'This email is already in used.')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read', 'identifier', 'timestamp']],
    denormalizationContext: ['groups' => ['user:write', 'user:update']],
)]
#[GetCollection(security: "is_granted('ROLE_ADMIN')")]
#[Get(security: "is_granted('ROLE_ADMIN') or object == user or (object.getAgency() and object.getAgency().getDirector() == user)")]
#[Post(
    security: "is_granted('ROLE_DIRECTOR')",
    validationContext: ['groups' => ['Default', 'user:write']],
    processor: UserPasswordHasher::class,
)]
#[Put(
    security: "is_granted('ROLE_ADMIN') or object == user or object.getAgency().getDirector() == user",
    //    securityPostDenormalize: "is_granted('ROLE_ADMIN') or (object == user and previous_object == user) or (object.getAgency().getDirector() == user and previous_object == object.getAgency().getDirector())",
    processor: UserPasswordHasher::class,
)]
#[Patch(
    security: "is_granted('ROLE_ADMIN') or object == user or object.getAgency().getDirector() == user",
    processor: UserPasswordHasher::class
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidTrait;
    use TimeStampTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The first name should not be blank.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your first name must be at least {{ limit }} characters long.',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters.',
    )]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The last name should not be blank.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The last name must be at least {{ limit }} characters long.',
        maxMessage: 'The last name cannot be longer than {{ limit }} characters.',
    )]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\NotBlank(message: 'The email should not be blank.')]
    #[Assert\Length(max: 180, maxMessage: 'The email cannot be longer than {{ limit }} characters.')]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var ?string The hashed password
     */
    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The password should not be blank.', groups: ['user:write'])]
    #[Assert\Length(
        min: 8,
        max: 100,
        minMessage: 'The password must be at least {{ limit }} characters long',
        maxMessage: 'The password cannot be longer than {{ limit }} characters',
    )]
    #[Assert\PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_MEDIUM, // Strong password required
    ])]
    #[Groups(['user:read', 'user:write', 'user:update'])]
    private ?string $plainPassword = null;

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The address should not be blank.', groups: ['user:write'])]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-\',]*$/', message: 'The {{ value }} is not a valid address.')]
    #[Assert\Length(max: 130, maxMessage: 'The address cannot be longer than {{ limit }} characters')]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 130, nullable: true)]
    private ?string $address = null;

    //    #[Assert\Date]
    #[Assert\NotBlank(message: 'The birth date should not be blank.', groups: ['user:write'])]
    #[Assert\LessThanOrEqual('-18 years', message: 'You should have 18 years old or more.')]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    /**
     * @var array<string> $roles
     */
    #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column]
    private array $roles = [];

    #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotBlank(message: 'The phone number should not be blank.', groups: ['user:write'])]
    #[Assert\Regex(pattern: '/^[0-9]{1,9}$/', message: 'The phone is not correctly formatted.')]
    #[Assert\Length(exactly: 9, exactMessage: 'The phone number should have exactly {{ limit }} characters.')]
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 9)]
    private ?string $phoneNumber = null;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column]
    private ?bool $verifiedEmail = false;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[ORM\Column]
    private ?bool $verifiedPhoneNumber = false;

    #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
    #[Assert\NotNull]
    #[Groups(['user:update'])]
    #[ORM\Column]
    private ?bool $active = true;

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

    /**
     * @var ArrayCollection<int, RentalArchived> $rentalsArchivedAsCustomer
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: RentalArchived::class)]
    private Collection $rentalsArchivedAsCustomer;

    /**
     * @var ArrayCollection<int, RentalArchived> $rentalsArchivedAsEmployee
     */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: RentalArchived::class)]
    private Collection $rentalsArchivedAsEmployee;

    public function __construct()
    {
        $this->myRentals = new ArrayCollection();
        $this->myManagedRentals = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->rentalsArchivedAsCustomer = new ArrayCollection();
        $this->rentalsArchivedAsEmployee = new ArrayCollection();
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

        // add the prefix to all role names
        $roles = array_map(fn ($role) => 'ROLE_'.$role, $roles);

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return ucfirst($this->firstName ?: '');
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return ucfirst($this->lastName ?: '');
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency instanceof Agency ? $this->agency : new Agency();
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

    public function isVerifiedEmail(): ?bool
    {
        return $this->verifiedEmail;
    }

    public function setVerifiedEmail(bool $verifiedEmail): static
    {
        $this->verifiedEmail = $verifiedEmail;

        return $this;
    }

    public function isVerifiedPhoneNumber(): ?bool
    {
        return $this->verifiedPhoneNumber;
    }

    public function setVerifiedPhoneNumber(bool $verifiedPhoneNumber): static
    {
        $this->verifiedPhoneNumber = $verifiedPhoneNumber;

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

    /**
     * @return Collection<int, RentalArchived>
     */
    public function getRentalsArchivedAsCustomer(): Collection
    {
        return $this->rentalsArchivedAsCustomer;
    }

    public function addRentalsArchivedAsCustomer(RentalArchived $rentalArchivedAsCustomer): static
    {
        if (!$this->rentalsArchivedAsCustomer->contains($rentalArchivedAsCustomer)) {
            $this->rentalsArchivedAsCustomer->add($rentalArchivedAsCustomer);
            $rentalArchivedAsCustomer->setCustomer($this);
        }

        return $this;
    }

    public function removeRentalArchivedAsCustomer(RentalArchived $rentalArchivedAsCustomer): static
    {
        if ($this->rentalsArchivedAsCustomer->removeElement($rentalArchivedAsCustomer)) {
            // set the owning side to null (unless already changed)
            if ($rentalArchivedAsCustomer->getCustomer() === $this) {
                $rentalArchivedAsCustomer->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RentalArchived>
     */
    public function getRentalsArchivedAsEmployee(): Collection
    {
        return $this->rentalsArchivedAsEmployee;
    }

    public function addRentalsArchivedAsEmployee(RentalArchived $rentalsArchivedAsEmployee): static
    {
        if (!$this->rentalsArchivedAsEmployee->contains($rentalsArchivedAsEmployee)) {
            $this->rentalsArchivedAsEmployee->add($rentalsArchivedAsEmployee);
            $rentalsArchivedAsEmployee->setEmployee($this);
        }

        return $this;
    }

    public function removeRentalsArchivedAsEmployee(RentalArchived $rentalsArchivedAsEmployee): static
    {
        if ($this->rentalsArchivedAsEmployee->removeElement($rentalsArchivedAsEmployee)) {
            // set the owning side to null (unless already changed)
            if ($rentalsArchivedAsEmployee->getEmployee() === $this) {
                $rentalsArchivedAsEmployee->setEmployee(null);
            }
        }

        return $this;
    }
}
