<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Table(name: 'tbl_student')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 50)]
    private ?string $className = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'students')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'id', nullable: false)]
    private ?School $school = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'students')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    // Getter and setters ...

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function getFirstName(): ?string
{
    return $this->firstName;
}

public function setFirstName(?string $firstName): self
{
    $this->firstName = $firstName;
    return $this;
}
// For lastName
public function getLastName(): ?string
{
    return $this->lastName;
}

public function setLastName(?string $lastName): self
{
    $this->lastName = $lastName;
    return $this;
}

// For phone
public function getPhone(): ?string
{
    return $this->phone;
}

public function setPhone(?string $phone): self
{
    $this->phone = $phone;
    return $this;
}

    /**
     * Récupérer la classe, le nom de l'établissement et l'email de l'utilisateur
     */
    public function getClassAndSchoolInfo(): string
    {
        // Récupérer le nom de la classe
        $class = $this->getClassName();

        // Récupérer le nom de l'établissement
        $schoolName = $this->getSchool() ? $this->getSchool()->getName() : 'Etablissement inconnu';

        // Récupérer l'email de l'utilisateur
        $userEmail = $this->getUser() ? $this->getUser()->getEmail() : 'Email inconnu';

        // Retourner un format lisible
        return "Classe: $class, Etablissement: $schoolName, Email: $userEmail";
    }
}
