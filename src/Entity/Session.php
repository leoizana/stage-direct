<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'tbl_session')]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $session_list = null;

    /**
     * @var Collection<int, internship>
     */
    #[ORM\OneToMany(targetEntity: internship::class, mappedBy: 'session')]
    private Collection $internship;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?school $school = null;

    public function __construct()
    {
        $this->internship = new ArrayCollection();
    }
    public function __toString(): string
{
    return $this->session_list;
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionList(): ?string
    {
        return $this->session_list;
    }

    public function setSessionList(string $session_list): static
    {
        $this->session_list = $session_list;

        return $this;
    }

    /**
     * @return Collection<int, internship>
     */
    public function getInternship(): Collection
    {
        return $this->internship;
    }

    public function addInternship(internship $internship): static
    {
        if (!$this->internship->contains($internship)) {
            $this->internship->add($internship);
            $internship->setSession($this);
        }

        return $this;
    }

    public function removeInternship(internship $internship): static
    {
        if ($this->internship->removeElement($internship)) {
            // set the owning side to null (unless already changed)
            if ($internship->getSession() === $this) {
                $internship->setSession(null);
            }
        }

        return $this;
    }

    public function getSchool(): ?school
    {
        return $this->school;
    }

    public function setSchool(?school $school): static
    {
        $this->school = $school;

        return $this;
    }
}
