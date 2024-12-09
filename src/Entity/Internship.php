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

    #[ORM\Column(type: 'string', length: 255)]
    private $classeEleve;

    #[ORM\Column(type: 'datetime')]
    private $dateDebut;

    #[ORM\Column(type: 'datetime')]
    private $dateFin;

    #[ORM\Column(type: 'string', length: 255)]
    private $session;

    #[ORM\Column(type: 'text')]
    private $themes;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClasseEleve(): ?string
    {
        return $this->classeEleve;
    }

    public function setClasseEleve(string $classeEleve): self
    {
        $this->classeEleve = $classeEleve;

        return $this;
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

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getThemes(): ?string
    {
        return $this->themes;
    }

    public function setThemes(string $themes): self
    {
        $this->themes = $themes;

        return $this;
    }
}