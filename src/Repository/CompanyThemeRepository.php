<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyTheme;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method CompanyTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyTheme[]    findAll()
 * @method CompanyTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyThemeRepository extends ServiceEntityRepository
{

    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, CompanyTheme::class);
    }

    public function getCompaniesByTheme(int $theme): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT IDENTITY(ct.company) FROM App\Entity\CompanyTheme ct WHERE ct.theme = :theme'
        )->setParameter('theme', $theme);

        $result = $query->getArrayResult();

        $companyRepository = new CompanyRepository($this->registry);
        $result = array_merge(...$result);

        return $companyRepository->findBy(['id' => $result]);

    }

    public function getOneByCompanyAndTheme(Company $company, Theme $theme): ?CompanyTheme
    {

        $query = $this->createQueryBuilder('ct')
            ->select('ct, c')
            ->join("ct.company", "c")
            ->where("ct.company = :company")
            ->andWhere("ct.theme = :theme")
            ->setParameter('company', $company)
            ->setParameter('theme', $theme)
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function getAllByCompany(Company $company): array
    {

        $query = $this->createQueryBuilder('ct')
            ->select('ct, t')
            ->join("ct.company", "c")
            ->join("ct.theme", "t")
            ->where("ct.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();

        return $query;
    }
}
