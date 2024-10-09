<?php

namespace App\Tests;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\StudentRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class StudentTest2Test extends WebTestCase
{

    /*public function testerURL(): void
    {
        $client = static::createClient();
        $client->request('GET', '/student');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Student index');
        
    }*/
    public function testConnexion(): void 
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('leo.germain2005@gmail.com');
        $client = static::createClient();
        $client->loginUser($testUser);
        $client->request('GET', '/school/new');
        $this->assertResponseIsSuccessful('gg wp');
    }


/*

class StudentTest2Test extends WebTestCase
{
    public function testName(): void
    {
        $userRepository = static::getContainer()->get(StudentRepository::class);
        $testUser = $userRepository->findOneById('1');
        $this->assertSame('Fabrice', $testUser->getFirstname());
    }
}

/*
class StudentTestTest extends TestCase
{
    public function testName(): void
    {
        $student = new Student();
        $student->setFirstname(firstname: "Fabrice");
        $student->setLastname("PINARD");
        $student->setAddress("Quelques parts...");
        $student->setZipcode('50540');
        $student->setTown('Dans une ville de Normandie');

        $this->assertSame('Fabrice', $student->getFirstname());

    }
}
*/


}
