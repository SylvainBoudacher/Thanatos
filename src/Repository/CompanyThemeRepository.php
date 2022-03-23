<?php

namespace App\Repository;

use App\Entity\CompanyTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

        $result =  $query->getArrayResult();

        $companyRepository = new CompanyRepository($this->registry);
        $result = array_merge(...$result);

        return $companyRepository->findBy(['id' => $result]);

    }

    // /**
    //  * @return CompanyTheme[] Returns an array of CompanyTheme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyTheme
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
