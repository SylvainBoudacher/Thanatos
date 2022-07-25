<?php

namespace App\Repository;

use App\Entity\DriverOrder;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DriverOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method DriverOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method DriverOrder[]    findAll()
 * @method DriverOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DriverOrder::class);
    }

    // /**
    //  * @return DriverOrder[] Returns an array of DriverOrder objects
    //  */

    public function findCurrentOrderDriverInProgress($company)
    {
        $status = [Order::DRIVER_ACCEPT,
            Order::DRIVER_ARRIVES,
            Order::DRIVER_PROCESSING_ACCEPT,
            Order::DRIVER_BRINGS_TO_WAREHOUSE];

        $query = $this->createQueryBuilder('do')
            ->join("do.driver", "d")
            ->join("do.command", "o")
            ->where("d = :driver")
            ->andWhere("o.status IN (:status)")
            ->setParameter('driver', $company)
            ->setParameter('status', $status)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findCurrentOrderDriverInProgressByCompanyAndOrder($company, $order)
    {
        $status = [Order::DRIVER_ACCEPT,
            Order::DRIVER_ARRIVES,
            Order::DRIVER_PROCESSING_ACCEPT,
            Order::DRIVER_BRINGS_TO_WAREHOUSE];

        $query = $this->createQueryBuilder('do')
            ->join("do.driver", "d")
            ->join("do.command", "o")
            ->where("d = :driver")
            ->andWhere("o.status IN (:status)")
            ->andWhere("o = :order")
            ->setParameter('driver', $company)
            ->setParameter('status', $status)
            ->setParameter('order', $order)
            ->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /*
    public function findOneBySomeField($value): ?DriverOrder
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
