<?php

namespace App\Repository;

use App\Entity\Corpse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Corpse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Corpse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Corpse[]    findAll()
 * @method Corpse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorpseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Corpse::class);
    }

    // /**
    //  * @return Corpse[] Returns an array of Corpse objects
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
    public function findOneBySomeField($value): ?Corpse
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
