<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'tbl_company')]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]  // Utilisation de la stratégie "AUTO" pour PostgreSQL
    #[ORM\Column(type: "integer")]  // Spécification du type pour PostgreSQL
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
