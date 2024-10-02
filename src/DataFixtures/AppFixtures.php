<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // Création du premier utilisateur
        $user = new User();
        $user->setEmail('user@domain.tld');
        $user->setFirstname('Jean-Michel');
        $user->setLastname('DEBROISE');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        // Création du premier étudiant
        $student = new User();
        $student->setEmail('user@domain.tld');
        $student->setFirstname('Jean-Michel');
        $student->setLastname('DEBROISE');
        $student->setRoles(['ROLE_STUDENT']);
        $manager->persist($student);


        // Création du premier teacher
        $teacher = new User();
        $teacher->setEmail('debroise@ndlpavranches.fr');
        $teacher->setFirstname('Jean-Michel');
        $teacher->setLastname('DEBROISE');
        $teacher->setRoles(['ROLE_TEACHER']);
        $manager->persist($teacher);

         // Création du premier admin
         $admin = new User();
         $admin->setEmail('debroise@ndlpavranches.fr');
         $admin->setFirstname('Jean-Michel');
         $admin->setLastname('DEBROISE');
         $admin->setRoles(['ROLE_ADMIN']);
         $manager->persist($admin);
 
        // Création du premier super admin
        $superadmin = new User();
        $superadmin->setEmail('debroise@ndlpavranches.fr');
        $superadmin->setFirstname('Jean-Michel');
        $superadmin->setLastname('DEBROISE');
        $superadmin->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($superadmin);


        $manager->flush();
    }
}
