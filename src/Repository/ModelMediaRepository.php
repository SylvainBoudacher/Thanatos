<?php

namespace App\Repository;

use App\Entity\ModelMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModelMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelMedia[]    findAll()
 * @method ModelMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModelMedia::class);
    }

    // /**
    //  * @return ModelMedia[] Returns an array of ModelMedia objects
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
    public function findOneBySomeField($value): ?ModelMedia
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
