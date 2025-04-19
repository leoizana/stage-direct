<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'tbl_company')]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]  // Utilisation de la stratégie "AUTO" pour PostgreSQL
    #[ORM\Column(type: "integer")]  // Spécification du type pour PostgreSQL
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;  // Nom de l'entreprise

    
    #[ORM\Column(type: "string", length: 255,  nullable: true)]
    private ?string $street = null;  // Rue

    #[ORM\Column(type: "string", length: 20)]
    private ?string $zipCode = null;  // Code Postal

    #[ORM\Column(type: "string", length: 255)]
    private ?string $city = null;  // Ville

    #[ORM\Column(type: "string", length: 255)]
    private ?string $country = null;  // Pays

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $phone = null;  // Téléphone

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = null;

    #[ORM\ManyToOne(inversedBy: 'relation')]
    private ?Activity $activity = null;

    #[ORM\Column(nullable: true)]
    private ?bool $IsVerified = null;

    /**
     * @var Collection<int, Internship>
     */
    #[ORM\OneToMany(targetEntity: Internship::class, mappedBy: 'company')]
    private Collection $internships;

    public function __construct()
    {
        $this->internships = new ArrayCollection();
    }  // Email

    // Getter et setter pour chaque propriété

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

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
            $internship->setCompany($this);
        }

        return $this;
    }

    public function removeInternship(Internship $internship): static
    {
        if ($this->internships->removeElement($internship)) {
            // set the owning side to null (unless already changed)
            if ($internship->getCompany() === $this) {
                $internship->setCompany(null);
            }
        }

        return $this;
    }
}
