<?php

namespace App\DataFixtures;

use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de la première école
      $school = new School();
      $school->setName("NDLP");
      $school->setAddress("9 Rue Chanoine Beranger");
      $school->setZipcode("50300");
      $school->setTown('Avranches');
      $manager->persist($school);


        $manager->flush();
    }
}
