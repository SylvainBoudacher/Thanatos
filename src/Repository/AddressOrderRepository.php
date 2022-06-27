<?php

namespace App\Repository;

use App\Entity\AddressOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method AddressOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressOrder[]    findAll()
 * @method AddressOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressOrderRepository extends ServiceEntityRepository
{
    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, AddressOrder::class);
        $this->security = $security;

    }

    // /**
    //  * @return AddressOrder[] Returns an array of AddressOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AddressOrder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findOneOwnedByStatusAndOrder($statusAddress, $statusOrder): ?AddressOrder
    {
        return $this->createQueryBuilder('a')
            ->select('o')
            ->from('App:Order', 'o')
            ->join('a.command')
            ->andWhere('a.status = :statusAddress')
            ->andWhere('a.command.status= :statusOrder')
            ->andWhere('a.command.possessor = :possessor')
            ->setParameter('statusAddress', $statusAddress)
            ->setParameter('statusOrder', $statusOrder)
            ->setParameter('possessor', $this->security->getUser())
            ->getQuery()
            ->getOneOrNullResult();
    }

}
