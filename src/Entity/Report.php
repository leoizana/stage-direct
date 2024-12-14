<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\Table(name: 'tbl_report')]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;  // Nouveau champ pour le prénom

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;   // Nouveau champ pour le nom

    #[ORM\Column(type: 'string', length: 255)]
    private string $professorEmail;  // Nouveau champ pour l'email du professeur

    #[ORM\Column(type: 'string', length: 255)]
    private string $session;   // Nouveau champ pour la session

    #[ORM\Column(type: 'string', length: 255)]
    private string $classe;    // Nouveau champ pour la classe

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt = null): self
    {
        if ($createdAt === null) {
            $createdAt = new \DateTime();
        }
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getProfessorEmail(): string
    {
        return $this->professorEmail;
    }

    public function setProfessorEmail(string $professorEmail): self
    {
        $this->professorEmail = $professorEmail;
        return $this;
    }

    // Getter et setter pour le champ session
    public function getSession(): string
    {
        return $this->session;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;
        return $this;
    }

    // Getter et setter pour le champ classe
    public function getClasse(): string
    {
        return $this->classe;
    }

    public function setClasse(string $classe): self
    {
        $this->classe = $classe;
        return $this;
    }
}
