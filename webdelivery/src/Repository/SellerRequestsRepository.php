<?php

namespace App\Repository;

use App\Entity\SellerRequests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SellerRequests|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellerRequests|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellerRequests[]    findAll()
 * @method SellerRequests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellerRequestsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SellerRequests::class);
    }

    public function findBySeller(int $id , $page = 1)
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere('s.seller = :id')
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

    public function findBySellerAndUser($sellerId , $userId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.seller = :sellerId')
            ->andWhere('s.user = :userId')
            ->setParameter('sellerId', $sellerId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return SellerRequests[] Returns an array of SellerRequests objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SellerRequests
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
