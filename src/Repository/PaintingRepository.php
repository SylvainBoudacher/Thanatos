<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Painting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Painting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Painting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Painting[]    findAll()
 * @method Painting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaintingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Painting::class);
    }

    public function getAllByCompany(Company $company) {

        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->join("p.companyPaintings", "cp")
            ->where("cp.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $query;
    }
}
