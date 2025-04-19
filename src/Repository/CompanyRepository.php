<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }
    public function findFiltered(?string $name, ?string $cityZip, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('c');
    
        if ($name) {
            $qb->andWhere('LOWER(c.name) LIKE :name')
               ->setParameter('name', '%' . strtolower($name) . '%');
        }
    
        if ($cityZip) {
            $qb->andWhere('LOWER(c.city) LIKE :cityZip OR c.zipCode LIKE :cityZip')
               ->setParameter('cityZip', '%' . strtolower($cityZip) . '%');
        }
    
        return $qb->setMaxResults($limit)
                  ->setFirstResult($offset)
                  ->getQuery()
                  ->getResult();
    }
    
    public function countFiltered(?string $name, ?string $cityZip): int
    {
        $qb = $this->createQueryBuilder('c')
                  ->select('COUNT(c.id)');
    
        if ($name) {
            $qb->andWhere('LOWER(c.name) LIKE :name')
               ->setParameter('name', '%' . strtolower($name) . '%');
        }
    
        if ($cityZip) {
            $qb->andWhere('LOWER(c.city) LIKE :cityZip OR c.zipCode LIKE :cityZip')
               ->setParameter('cityZip', '%' . strtolower($cityZip) . '%');
        }
    
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return Company[] Returns an array of Company objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Company
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
