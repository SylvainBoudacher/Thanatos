<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyExtra;
use App\Entity\CompanyMaterial;
use App\Entity\Material;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    public function getOneByCompanyAndMaterial(Company $company, Material $material): ?CompanyMaterial
    {

        $query = $this->createQueryBuilder('cm')
            ->select('cm, c')
            ->join("cm.company", "c")
            ->where("cm.company = :company")
            ->andWhere("cm.material = :material")
            ->setParameter('company', $company)
            ->setParameter('material', $material)
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
