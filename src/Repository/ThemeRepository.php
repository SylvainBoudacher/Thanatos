<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyTheme;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[]    findAll()
 * @method Theme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    public function getAllByCompany(Company $company) {

        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->join("t.companyThemes", "ct")
            ->where("ct.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $query;
    }
}
