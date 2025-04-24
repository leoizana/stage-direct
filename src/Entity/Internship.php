<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\InternshipRepository;

#[ORM\Entity(repositoryClass: InternshipRepository::class)]
#[ORM\Table(name: 'tbl_internship')]
class Internship
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]  // Définir l'auto-incrémentation pour la base de données
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $dateDebut;

    #[ORM\Column(type: 'datetime')]
    private $dateFin;

    #[ORM\Column(type: 'text')]
    private $report;

    #[ORM\ManyToOne(inversedBy: 'internships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $relation = null;

    #[ORM\ManyToOne(inversedBy: 'internship')]
    private ?Session $session = null;

    #[ORM\Column(nullable: true)]
    private ?bool $IsVerified = null;

    #[ORM\ManyToOne(inversedBy: 'internships')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Company $Company = null;

     #[ORM\Column(type: 'text', nullable: true)]
    private ?string $teacherReport = null;
    
       
    // Getters et setters
    public function getTeacherReport(): ?string
    {
        return $this->teacherReport;
    }

    public function setTeacherReport(?string $teacherReport): self
    {
        $this->teacherReport = $teacherReport;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getreport(): ?string
    {
        return $this->report;
    }

    public function setreport(string $report): self
    {
        $this->report = $report;

        return $this;
    }
    
    public function getRelation(): ?User
    {
        return $this->relation;
    }

    public function setRelation(?User $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->IsVerified;
    }

    public function setVerified(?bool $IsVerified): static
    {
        $this->IsVerified = $IsVerified;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->Company;
    }

    public function setCompany(?Company $Company): static
    {
        $this->Company = $Company;

        return $this;
    }
}