<?php

namespace App\Repository;

use App\Entity\AddressOrder;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
    public function findOneOwnedByStatusAndOrder($statusAddress, $statusOrder, $order): ?AddressOrder
    {
        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->innerJoin(Order::class, 'o', 'WITH', 'o.id = a.command')
            ->andWhere('a.status = :statusAddress')
            ->andWhere('o.status= :statusOrder')
            ->andWhere('o.possessor = :possessor')
            ->andWhere('o = :order')
            ->setParameter('statusAddress', $statusAddress)
            ->setParameter('statusOrder', $statusOrder)
            ->setParameter('possessor', $this->security->getUser())
            ->setParameter('order', $order)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

}
