<?php

namespace App\Repository;

use App\Entity\CompanyMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyMaterial[]    findAll()
 * @method CompanyMaterial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyMaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyMaterial::class);
    }

    // /**
    //  * @return CompanyMaterial[] Returns an array of CompanyMaterial objects
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
    public function findOneBySomeField($value): ?CompanyMaterial
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
