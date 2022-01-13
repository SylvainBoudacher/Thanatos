<?php

namespace App\Repository;

use App\Entity\CompanyExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyExtra[]    findAll()
 * @method CompanyExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyExtra::class);
    }

    // /**
    //  * @return CompanyExtra[] Returns an array of CompanyExtra objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyExtra
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
