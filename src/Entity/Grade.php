<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
#[ORM\Table(name: 'tbl_grade')]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $className = null;

    #[ORM\ManyToMany(targetEntity: School::class, mappedBy: 'grades')]
    private Collection $schools;

    public function __construct()
    {
        $this->schools = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }

    public function addSchool(School $school): self
    {
        if (!$this->schools->contains($school)) {
            $this->schools[] = $school;
            $school->addGrade($this);
        }

        return $this;
    }

    public function removeSchool(School $school): self
    {
        if ($this->schools->removeElement($school)) {
            $school->removeGrade($this);
        }

        return $this;
    }
}
