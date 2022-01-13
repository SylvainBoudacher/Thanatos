<?php

namespace App\Repository;

use App\Entity\ModelMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModelMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelMaterial[]    findAll()
 * @method ModelMaterial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelMaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModelMaterial::class);
    }

    // /**
    //  * @return ModelMaterial[] Returns an array of ModelMaterial objects
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
    public function findOneBySomeField($value): ?ModelMaterial
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
