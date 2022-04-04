<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyPainting;
use App\Entity\Painting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyPainting|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyPainting|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyPainting[]    findAll()
 * @method CompanyPainting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyPaintingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyPainting::class);
    }

    public function getOneByCompanyAndPainting(Company $company, Painting $painting) : ?CompanyPainting {

        $query = $this->createQueryBuilder('cp')
            ->select('cp, c')
            ->join("cp.company", "c")
            ->where("cp.company = :company")
            ->andWhere("cp.painting = :painting")
            ->setParameter('company', $company)
            ->setParameter('painting', $painting)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $query;
    }


}
