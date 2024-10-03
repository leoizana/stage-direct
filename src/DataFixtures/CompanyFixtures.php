<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de la première entreprise
      $company1 = new Company();
      $company1->setName("STURNO");
      $manager->persist($company1);

      // Création de la deuxième entreprise
      $company2 = new Company();
      $company2->setName("LactaTraite");
      $manager->persist($company2);

        $manager->flush();
    }
}
