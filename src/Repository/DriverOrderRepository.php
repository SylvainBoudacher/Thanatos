<?php

namespace App\Repository;

use App\Entity\DriverOrder;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DriverOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method DriverOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method DriverOrder[]    findAll()
 * @method DriverOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DriverOrder::class);
    }

    // /**
    //  * @return DriverOrder[] Returns an array of DriverOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DriverOrder
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
