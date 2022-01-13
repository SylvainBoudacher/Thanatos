<?php

namespace App\Repository;

use App\Entity\ModelExtra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModelExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelExtra[]    findAll()
 * @method ModelExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModelExtra::class);
    }

    // /**
    //  * @return ModelExtra[] Returns an array of ModelExtra objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModelExtra
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
