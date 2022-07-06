<?php

namespace App\Repository;

use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\CompanyPainting;
use App\Entity\CompanyTheme;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\ModelMaterial;
use App\Entity\Painting;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function getCompleteProductsRelatedToCompany(Company $company)
    {

        return $this->createQueryBuilder('m')
            ->select('m', 'b', 'model_material', 'model_extra', 'cp')
            ->innerJoin(Company::class, 'c', 'WITH', 'c.id = m.company')
            ->innerJoin(Burial::class, 'b', 'WITH', 'b.id = m.burial')
            ->innerJoin(ModelMaterial::class, 'model_material', 'WITH', 'model_material.model = m.id')
            ->innerJoin(ModelExtra::class, 'model_extra', 'WITH', 'model_extra.model = m.id')
            ->innerJoin(CompanyPainting::class, 'cp', 'WITH', 'cp.company = c.id')
            ->where('c = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();
    }

    public function getCompaniesThatHaveModelsAndFiltersByTheme(Theme $theme)
    {

        $query = $this->createQueryBuilder('m')
            ->select('c')
            ->innerJoin(Company::class, 'c', 'WITH', 'c.id = m.company')
            ->innerJoin(CompanyTheme::class, 'ct', 'WITH', 'ct.company = c.id')
            ->where('ct.theme = :theme')
            ->groupBy('c')
            ->setParameter('theme', $theme)
            ->getQuery();

        return $query->execute();
    }

    // /**
    //  * @return Model[] Returns an array of Model objects
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
    public function findOneBySomeField($value): ?Model
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
