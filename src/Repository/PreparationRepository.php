<?php

namespace App\Repository;

use App\Entity\Preparation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Preparation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preparation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preparation[]    findAll()
 * @method Preparation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreparationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preparation::class);
    }

    public function getPreparationsByCompany($company)
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('App\Entity\ModelMaterial', 'mm', 'WITH', 'mm = p.modelMaterial')
            ->innerJoin('App\Entity\ModelExtra', 'me', 'WITH', 'me = p.modelExtra')
            ->innerJoin('App\Entity\Model', 'model', 'WITH', 'model = mm.model and model = me.model')
            ->join('App\Entity\Company', 'c', 'WITH', 'c = model.company')
            ->where('c = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $query;
    }

    public function getPreparationsByCompanyAndTheme($company, $theme)
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('App\Entity\ModelMaterial', 'mm', 'WITH', 'mm = p.modelMaterial')
            ->innerJoin('App\Entity\ModelExtra', 'me', 'WITH', 'me = p.modelExtra')
            ->innerJoin('App\Entity\Model', 'model', 'WITH', 'model = mm.model and model = me.model')
            ->join('App\Entity\Company', 'c', 'WITH', 'c = model.company')
            ->where('c = :company')
            ->andWhere('p.theme = :theme')
            ->setParameter('company', $company)
            ->setParameter('theme', $theme)
            ->getQuery()
            ->execute();

        return $query;
    }

    public function getPreparationsNewNotOccupied()
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.status IN (:status)')
            ->andWhere('p.driver is NULL')
            ->setParameter('status', [Preparation::FUNERAL_ACCEPT, Preparation::FUNERAL_CLOSE_PROCESSING])
            ->getQuery()
            ->execute();

        return $query;
    }

    public function findCurrentOrderDriverInProgress($company)
    {
        $status = [
            Preparation::FUNERAL_DRIVER_ACCEPT_TO_BRINGS_TO_FUNERAL,
//            Preparation::FUNERAL_DRIVER_BRINGS_TO_FUNERAL,
//            Preparation::FUNERAL_CORPSE_ARRIVES_TO_FUNERAL,
            Preparation::FUNERAL_DRIVER_ACCEPT_BRINGS_TO_USER,
        ];

        $query = $this->createQueryBuilder('p')
            ->where('p.status IN (:status)')
            ->andWhere('p.driver = :company')
            ->setParameter('status', $status)
            ->setParameter('company', $company)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function getPreparationsByCompanyByStatus($company, $status)
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('App\Entity\ModelMaterial', 'mm', 'WITH', 'mm = p.modelMaterial')
            ->innerJoin('App\Entity\ModelExtra', 'me', 'WITH', 'me = p.modelExtra')
            ->innerJoin('App\Entity\Model', 'model', 'WITH', 'model = mm.model and model = me.model')
            ->join('App\Entity\Company', 'c', 'WITH', 'c = model.company')
            ->where('c = :company')
            ->andWhere('p.status IN (:status)')
            ->setParameter('company', $company)
            ->setParameter('status', $status)
            ->getQuery()
            ->execute();

        return $query;
    }

    // /**
    //  * @return Preparation[] Returns an array of Preparation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Preparation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
