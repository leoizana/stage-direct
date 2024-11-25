<?php

namespace App\Entity;

use App\Repository\StageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageRepository::class)]
#[ORM\Table(name: 'tbl_stage')]
class Stage
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
