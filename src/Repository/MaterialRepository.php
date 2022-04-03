<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Material;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Material|null find($id, $lockMode = null, $lockVersion = null)
 * @method Material|null findOneBy(array $criteria, array $orderBy = null)
 * @method Material[]    findAll()
 * @method Material[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Material::class);
    }

    public function getAllByCompany(Company $company) {

        $query = $this->createQueryBuilder('m')
            ->select('m')
            ->join("m.companyMaterials", "cm")
            ->where("cm.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $query;
    }
}
