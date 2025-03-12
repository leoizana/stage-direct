<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'tbl_user')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    // --- Ajout des nouveaux champs ---
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'boolean', nullable: true, options: ["default" => false])]
    private ?bool $isApprovedByTeacher = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    // --- Nouveau champ isVerified ---
    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;  // Défaut à false

    // --- Getters et Setters pour les nouveaux champs ---
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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

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

    // --- Nouveau getter et setter pour isVerified ---
    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // --- Getters et setters déjà existants ---
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
    public function getIsApprovedByTeacher(): bool
    {
        return $this->isApprovedByTeacher ?? false;
    }


    public function setIsApprovedByTeacher(bool $isApprovedByTeacher): static
    {
        $this->isApprovedByTeacher = $isApprovedByTeacher;

        return $this;
    }


    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    private function getHighestRole($roles)
    {
        $roleHierarchy = [
            'ROLE_SUPER_ADMIN' => 5,
            'ROLE_ADMIN' => 4,
            'ROLE_TEACHER' => 3,
            'ROLE_STUDENT' => 2,
            'ROLE_USER' => 1
        ];

        $highestRole = 'ROLE_USER';
        $highestPriority = 0;

        foreach ($roles as $role) {
            if (isset($roleHierarchy[$role]) && $roleHierarchy[$role] > $highestPriority) {
                $highestRole = $role;
                $highestPriority = $roleHierarchy[$role];
            }
        }

        return $highestRole;
    }


    // --- Role Handling ---
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Assurer que tous les utilisateurs ont au moins le rôle ROLE_USER
        if ($this->getIsApprovedByTeacher()) {
            $roles[] = 'ROLE_STUDENT';
        }
        $roles = $this->getHighestRole($roles);
        return array_unique([$roles]);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    // --- Verification Token ---
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $verificationToken = null;

    /**
     * @var Collection<int, Internship>
     */
    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'relation', orphanRemoval: true)]
    private Collection $internships;


    #[ORM\Column]
    private ?int $phone = null;

    /**
     * @var Collection<int, Grade>
     */
    #[ORM\ManyToMany(targetEntity: Grade::class, inversedBy: 'users')]
    private Collection $grade;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?School $school = null;

    public function __construct()
    {
        $this->internships = new ArrayCollection();
        $this->grade = new ArrayCollection();
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    // --- Clean up sensitive information ---
    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles, nettoyez-les ici
    }

    /**
     * @return Collection<int, Internship>
     */
    public function getInternships(): Collection
    {
        return $this->internships;
    }

    public function addInternship(Internship $internship): static
    {
        if (!$this->internships->contains($internship)) {
            $this->internships->add($internship);
            $internship->setRelation($this);
        }

        return $this;
    }

    public function removeInternship(Internship $internship): static
    {
        if ($this->internships->removeElement($internship)) {
            // set the owning side to null (unless already changed)
            if ($internship->getRelation() === $this) {
                $internship->setRelation(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrade(): Collection
    {
        return $this->grade;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grade->contains($grade)) {
            $this->grade->add($grade);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        $this->grade->removeElement($grade);

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }
}