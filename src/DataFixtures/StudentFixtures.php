<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StudentFixtures extends Fixture
{
    public const NDLP_SCHOOL_REFERENCE = 'NDLP';
    public function load(ObjectManager $manager): void
    {
    // Création du premier étudiant
      $student = new Student();
      $student->setFirstname(firstname: "Fabrice");
      $student->setLastname("PINARD");
      $student->setAddress("Quelques parts...");
      $student->setZipcode('50540');
      $student->setTown('Dans une ville de Normandie');
      $student->addSchool($this->getReference(StudentFixtures::NDLP_SCHOOL_REFERENCE));

      $manager->persist($student);

      $this->addReference(self::NDLP_SCHOOL_REFERENCE, $student);
        $manager->flush();
    }
}
