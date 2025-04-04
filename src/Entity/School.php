<?php

// src/Entity/School.php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[ORM\Table(name: 'tbl_school')]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    private ?string $zipcode = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    private array $newGrades = [];

    private array $newSessions = [];

    #[ORM\ManyToMany(targetEntity: Grade::class, inversedBy: 'schools')]
    #[ORM\JoinTable(name: 'school_grades')]
    private Collection $grades;

    // Propriété virtuelle pour gérer l'ajout de nouvelles classes (non persistée)
    private ?string $newGrade = null;

    private ?string $newSession = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'school')]
    private Collection $users;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'school')]
    private Collection $sessions;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    // Getters et setters pour les propriétés persistantes
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): self
    {
        if (!$this->grades->contains($grade)) {
            $this->grades[] = $grade;
        }

        return $this;
    }

    public function removeGrade(Grade $grade): self
    {
        $this->grades->removeElement($grade);

        return $this;
    }

    // Getter et Setter
public function getNewGrades(): array
{
    return $this->newGrades;
}

public function setNewGrades(array $newGrades): self
{
    $this->newGrades = $newGrades;
    return $this;
}

public function getNewSessions(): array
{
    return $this->newSessions;
}

public function setNewSessions(array $newSessions): self
{
    $this->newSessions = $newSessions;
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
        $user->setSchool($this);
    }

    return $this;
}

public function removeUser(User $user): static
{
    if ($this->users->removeElement($user)) {
        // set the owning side to null (unless already changed)
        if ($user->getSchool() === $this) {
            $user->setSchool(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Session>
 */
public function getSessions(): Collection
{
    return $this->sessions;
}

public function addSession(Session $session): static
{
    if (!$this->sessions->contains($session)) {
        $this->sessions->add($session);
        $session->setSchool($this);
    }

    return $this;
}

public function removeSession(Session $session): static
{
    if ($this->sessions->removeElement($session)) {
        // set the owning side to null (unless already changed)
        if ($session->getSchool() === $this) {
            $session->setSchool(null);
        }
    }

    return $this;
}
}
