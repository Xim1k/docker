<?php

namespace App\Repository;

use App\Entity\AdminRequests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AdminRequests|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminRequests|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminRequests[]    findAll()
 * @method AdminRequests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminRequestsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AdminRequests::class);
    }

    public function findAllPaginate($page = 1)
    {
        $query = $this->createQueryBuilder('s')
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
    //  * @return AdminRequests[] Returns an array of AdminRequests objects
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
    public function findOneBySomeField($value): ?AdminRequests
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
