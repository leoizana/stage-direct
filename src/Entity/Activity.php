<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'tbl_activity')]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * @var Collection<int, company>
     */
    #[ORM\OneToMany(targetEntity: company::class, mappedBy: 'activity')]
    private Collection $company;

    public function __construct()
    {
        $this->company = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, company>
     */
    public function getCompany(): Collection
    {
        return $this->company;
    }

    public function addCompany(company $company): static
    {
        if (!$this->company->contains($company)) {
            $this->company->add($company);
            $company->setActivity($this);
        }

        return $this;
    }

    public function removeCompany(company $company): static
    {
        if ($this->company->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getActivity() === $this) {
                $company->setActivity(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
    return $this->name ?? 'Undefined'; // Assurez-vous que `name` est bien défini dans `Activity`
}
}
