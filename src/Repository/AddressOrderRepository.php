<?php

namespace App\Repository;

use App\Entity\AddressOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AddressOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressOrder[]    findAll()
 * @method AddressOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressOrder::class);
    }

    // /**
    //  * @return AddressOrder[] Returns an array of AddressOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AddressOrder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
