<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Extra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Extra|null find($id, $lockMode = null, $lockVersion = null)
 * @method Extra|null findOneBy(array $criteria, array $orderBy = null)
 * @method Extra[]    findAll()
 * @method Extra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Extra::class);
    }

    public function getAllByCompany(Company $company) {

        $query = $this->createQueryBuilder('e')
            ->select('e')
            ->join("e.companyExtras", "ce")
            ->where("ce.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $query;
    }
}
