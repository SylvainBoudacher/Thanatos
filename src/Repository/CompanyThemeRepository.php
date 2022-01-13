<?php

namespace App\Repository;

use App\Entity\CompanyTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyTheme[]    findAll()
 * @method CompanyTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyTheme::class);
    }

    // /**
    //  * @return CompanyTheme[] Returns an array of CompanyTheme objects
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
    public function findOneBySomeField($value): ?CompanyTheme
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
