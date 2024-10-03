<?php

namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
   private $passwordHasher;
   public function __construct(UserPasswordHasherInterface $passwordHasher)
   {
      $this->passwordHasher = $passwordHasher;
   }
   public function load(ObjectManager $manager): void
   {

      // Création du premier user
      $user = new User();
      $user->setEmail('vincentg@gmail.com');
      $user->setPassword(
         $this->passwordHasher->hashPassword(
            $user,
            'Dawson'
         )
      );
      $user->setVerified(true);
      $user->setFirstname('Vincent');
      $user->setLastname('Gamblin');
      $user->setRoles(['ROLE_USER']);
      $manager->persist($user);


      // Création du premier étudiant
      $student = new User();
      $student->setVerified(true);
      $student->setEmail('MathisLaveille@gmail.com');
      $student->setPassword($this->passwordHasher->hashPassword(
         $student,
         'MathisKun'
      ));
      $student->setFirstname('Mathis');
      $student->setLastname('Laveille');
      $student->setRoles(['ROLE_STUDENT']);
      $manager->persist($student);

      // Création du premier teacher
      $teacher = new User();
      $teacher->setVerified(true);
      $teacher->setEmail('emericgralleeeee@gmail.com');
      $teacher->setPassword($this->passwordHasher->hashPassword(
         $teacher,
         'UwU'
      ));
      $teacher->setFirstname('Emeric');
      $teacher->setLastname('Grall');
      $teacher->setRoles(['ROLE_TEACHER']);
      $manager->persist($teacher);

      // Création du premier admin
      $admin = new User();
      $admin->setVerified(true);
      $admin->setEmail('leo.germain2005@gmail.com');
      $admin->setPassword($this->passwordHasher->hashPassword(
         $admin,
         'The GOAT'
      ));
      $admin->setFirstname('Léo');
      $admin->setLastname('GERMAIN');
      $admin->setRoles(['ROLE_ADMIN']);
      $manager->persist($admin);

      // Création du premier super admin
      $superadmin = new User();
      $superadmin->setVerified(true);
      $superadmin->setEmail('debroise@ndlpavranches.fr');
      $superadmin->setPassword($this->passwordHasher->hashPassword(
         $superadmin,
         'PDG'
      ));
      $superadmin->setFirstname('Jean-Marie');
      $superadmin->setLastname('Debroise');
      $superadmin->setRoles(['ROLE_SUPER_ADMIN']);
      $manager->persist($superadmin);


      $manager->flush();
   }
}