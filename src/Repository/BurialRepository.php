<?php

namespace App\Repository;

use App\Entity\Burial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Burial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Burial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Burial[]    findAll()
 * @method Burial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BurialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Burial::class);
    }

    // /**
    //  * @return Burial[] Returns an array of Burial objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Burial
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
