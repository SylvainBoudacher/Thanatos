<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyExtra;
use App\Entity\CompanyPainting;
use App\Entity\Extra;
use App\Entity\Painting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyExtra[]    findAll()
 * @method CompanyExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyExtra::class);
    }

    public function getOneByCompanyAndExtra(Company $company, Extra $extra): ?CompanyExtra
    {

        $query = $this->createQueryBuilder('ce')
            ->select('ce, c')
            ->join("ce.company", "c")
            ->where("ce.company = :company")
            ->andWhere("ce.extra = :extra")
            ->setParameter('company', $company)
            ->setParameter('extra', $extra)
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

}
