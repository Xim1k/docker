<?php

namespace App\Repository;

use App\Entity\Checkout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Checkout|null find($id, $lockMode = null, $lockVersion = null)
 * @method Checkout|null findOneBy(array $criteria, array $orderBy = null)
 * @method Checkout[]    findAll()
 * @method Checkout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckoutRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Checkout::class);
    }

    public function countDoneOrders(\DateTime $date)
    {

        $ql = $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('YEAR(d.createdAt) = :year')
            ->andWhere('MONTH(d.createdAt) = :month')
            ->andWhere('DAY(d.createdAt) = :day')
            ->andWhere('DAY(d.status) = :status')
            ->setParameter('year', $date->format('Y'))
            ->setParameter('month', $date->format('m'))
            ->setParameter('day', $date->format('d'))
            ->setParameter('status', Checkout::STATUS_DONE);
        ;
        return $ql->getQuery()->getSingleScalarResult();
    }

    public function countCancelOrders(\DateTime $date)
    {

        $ql = $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('YEAR(d.createdAt) = :year')
            ->andWhere('MONTH(d.createdAt) = :month')
            ->andWhere('DAY(d.createdAt) = :day')
            ->andWhere('DAY(d.status) = :status')
            ->setParameter('year', $date->format('Y'))
            ->setParameter('month', $date->format('m'))
            ->setParameter('day', $date->format('d'))
            ->setParameter('status', Checkout::STATUS_CANCEL);
        ;
        return $ql->getQuery()->getSingleScalarResult();
    }

    public function findBySeller(int $id , $page = 1)
    {
        $query = $this->createQueryBuilder('d'          )
            ->andWhere('d.seller = :id')
            ->setParameter('id', $id)
        ;

        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function findAllPaginate($page = 1)
    {
        $query = $this->createQueryBuilder('d')
        ;

        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function findByUser(int $id , $page = 1)
    {
        $query = $this->createQueryBuilder('d'          )
            ->andWhere('d.user = :id')
            ->setParameter('id', $id)
        ;

        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function paginate($dql, $page = 1, $limit = 4)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
        return $paginator;
    }

    // /**
    //  * @return Checkout[] Returns an array of Checkout objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Checkout
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
