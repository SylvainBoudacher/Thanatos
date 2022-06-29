<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findLastFinishedLimitByUser(int $id, int $limit = 3): array
    {
        // automatically knows to select Products
        // the "p" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->andWhere('o.possessor = :user')
            ->setParameter('status',Order::FINISHED)
            ->setParameter('user', $id)
            ->orderBy('o.updatedAt', 'DESC')
            ->setMaxResults( $limit );

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findMyCurrentOrder(int $user)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.status != :status')
            ->andWhere('o.possessor = :user')
            ->setParameter('status',Order::FINISHED)
            ->setParameter('user', $user)
            ->orderBy('o.updatedAt', 'DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findMyCurrentOrderDriver(int $user, array $arrayStatus)
    {
        $qb = $this->createQueryBuilder('o')
            ->where("o.status IN(:status)")
            ->andWhere('o.possessor = :user')
            ->setParameter('status', array_values($arrayStatus))
            ->setParameter('user', $user)
            ->orderBy('o.updatedAt', 'DESC')
            ->setMaxResults( 1 );
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function FindAllOrder()
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.types = :types')
            ->where('o.status = :status')
            ->setParameter('types', $type)
            ->setParameter('status', $status)
            ->orderBy('o.updatedAt','DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllOrderWhenTypeWhitStatus($type , $status)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.types = :types')
            ->andwhere('o.status = :status')
            ->setParameter('types', $type)
            ->setParameter('status', $status)
            ->orderBy('o.updatedAt','DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllOrderWhenTypeWithoutStatus($type , $status)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.types = :types')
            ->where('o.status != :status')
            ->setParameter('types', $type)
            ->setParameter('status', $status)
            ->orderBy('o.updatedAt','DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllOrderWhenType($type)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.types = :types')
            ->setParameter('types', $type)
            ->orderBy('o.updatedAt','DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllOrderWhenStatus($status)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.updatedAt','DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findAllOrderWithoutStatus($status)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.status != :status')
            ->setParameter('status', $status)
            ->orderBy('o.updatedAt','DESC');
        $query = $qb->getQuery();
        return $query->execute();
    }




}
