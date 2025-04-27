<?php

namespace App\Repository;

use App\Entity\Internship;
use App\Entity\Session;
use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Internship>
 */
class InternshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Internship::class);
    }
    public function findAllSessions()
    {
        return $this->getEntityManager()->getRepository(Session::class)->findAll();
    }
    public function findAllGrades()
    {
        return $this->getEntityManager()->getRepository(Grade::class)->findAll();
    }
    public function findFiltered(?string $title, ?string $cityZip, ?int $limit = null, ?int $offset = null, ?bool $IsVerified = null): array
{
    $qb = $this->createQueryBuilder('i');

    if ($title) {
        $qb->andWhere('LOWER(i.title) LIKE :title')
           ->setParameter('title', '%' . strtolower($title) . '%');
    }

    if ($cityZip) {
        $qb->andWhere('LOWER(i.city) LIKE :cityZip OR i.zipCode LIKE :cityZip')
           ->setParameter('cityZip', '%' . strtolower($cityZip) . '%');
    }

    if ($IsVerified !== null) {
        $qb->andWhere('i.IsVerified = :IsVerified')
           ->setParameter('IsVerified', $IsVerified);
    }

    if ($limit !== null) {
        $qb->setMaxResults($limit);
    }

    if ($offset !== null) {
        $qb->setFirstResult($offset);
    }

    return $qb->getQuery()->getResult();
}

public function countFiltered(?string $title, ?string $cityZip, ?bool $IsVerified = null): int
{
    $qb = $this->createQueryBuilder('i')
               ->select('COUNT(i.id)');

    if ($title) {
        $qb->andWhere('LOWER(i.title) LIKE :title')
           ->setParameter('title', '%' . strtolower($title) . '%');
    }

    if ($cityZip) {
        $qb->andWhere('LOWER(i.city) LIKE :cityZip OR i.zipCode LIKE :cityZip')
           ->setParameter('cityZip', '%' . strtolower($cityZip) . '%');
    }

    if ($IsVerified !== null) {
        $qb->andWhere('i.IsVerified = :IsVerified')
           ->setParameter('IsVerified', $IsVerified);
    }

    return (int) $qb->getQuery()->getSingleScalarResult();
}

//    /**
//     * @return Stage[] Returns an array of Stage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Stage
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
