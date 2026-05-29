<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Grade;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserRegistrationTest extends WebTestCase
{
    public function testUserCanRegisterSuccessfully(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get(EntityManagerInterface::class);
        $grade = (new Grade())->setClassName('Test '.uniqid('', true));
        $entityManager->persist($grade);
        $entityManager->flush();

        $uniqueEmail = 'test-'.uniqid('', true).'@example.com';

        try {
            $crawler = $client->request('GET', '/user/inscription');

            self::assertResponseIsSuccessful();

            $form = $crawler->selectButton("S'inscrire")->form();

            $form['user[firstName]'] = 'Marie';
            $form['user[lastName]'] = 'Dupont';
            $form['user[birthDate]'] = '2000-01-01';
            $form['user[phone]'] = '0600000000';
            $form['user[address]'] = '1 rue de test';
            $form['user[postalCode]'] = '75001';
            $form['user[city]'] = 'Paris';
            $form['user[email]'] = $uniqueEmail;
            $form['user[grade]'] = [(string) $grade->getId()];
            $form['user[password]'] = 'Password123!';

            $client->submit($form);

            self::assertResponseRedirects('/login');

            $createdUser = $container->get('doctrine')->getRepository(\App\Entity\User::class)->findOneBy([
                'email' => $uniqueEmail,
            ]);

            self::assertNotNull($createdUser);
            self::assertSame('Marie', $createdUser->getFirstName());
            self::assertSame('Dupont', $createdUser->getLastName());
            self::assertSame('75001', $createdUser->getPostalCode());
            self::assertFalse($createdUser->getIsVerified());
            self::assertSame(['ROLE_USER'], $createdUser->getRoles());
            self::assertNotEmpty($createdUser->getVerificationToken());
            self::assertNotSame('Password123!', $createdUser->getPassword());
        } finally {
            $userRepository = $container->get('doctrine')->getRepository(User::class);
            $createdUser = $userRepository->findOneBy(['email' => $uniqueEmail]);

            if ($createdUser !== null) {
                $managedUser = $entityManager->find(User::class, $createdUser->getId());

                if ($managedUser !== null) {
                    $entityManager->remove($managedUser);
                    $entityManager->flush();
                }
            }

            $managedGrade = $entityManager->find(Grade::class, $grade->getId());

            if ($managedGrade !== null) {
                $entityManager->remove($managedGrade);
                $entityManager->flush();
            }
        }
    }
}