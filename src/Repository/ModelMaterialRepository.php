<?php

namespace App\Repository;

use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\CompanyMaterial;
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

    public function getCompleteModelsProductsRelatedToCompany(Company $company)
    {
        $data['burials'] = $this->createQueryBuilder('model_material')
            ->select('b')
            ->join('App\Entity\Burial', 'b', 'WITH', 'b.id = model_material.model')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->where('cm.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
        ->execute();

        $data['modelMaterials'] = $this->createQueryBuilder('model_material')
            ->select('model_material')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->where('cm.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        $data['materials'] = $this->createQueryBuilder('model_material')
            ->select('m')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->join('App\Entity\Material', 'm', 'WITH', 'm.id = model_material.material')
            ->where('cm.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        $data['companyMaterials'] = $this->createQueryBuilder('model_material')
            ->select('cm')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->where('cm.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $data;
    }

    public function getBurialsByCompany(Company $company)
    {
        return $this->createQueryBuilder('model_material')
            ->select('b')
            ->join('App\Entity\Burial', 'b', 'WITH', 'b.id = model_material.model')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->where('cm.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

    }

    public function getByCompanyAndBurial(Company $company, Burial $burial)
    {
        $query = $this->createQueryBuilder('model_material')
            ->select('model_material')
            ->join('App\Entity\Burial', 'b', 'WITH', 'b = model_material.model')
            ->join('App\Entity\Model', 'model', 'WITH', 'model = model_material.model')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->where('cm.company = :company')
            ->andWhere('model.burial = :burial')
            ->setParameter('company', $company)
            ->setParameter('burial', $burial)
            ->getQuery()
            ->execute();

        return $query;
    }

    public function getByCompany(Company $company)
    {
        $query = $this->createQueryBuilder('model_material')
            ->select('model_material')
            ->join('App\Entity\Model', 'model', 'WITH', 'model = model_material.model')
            ->join('App\Entity\CompanyMaterial', 'cm', 'WITH', 'cm.material = model_material.material')
            ->where('cm.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();
        return $query;
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
