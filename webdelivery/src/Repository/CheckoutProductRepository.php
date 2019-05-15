<?php

namespace App\Repository;

use App\Entity\CheckoutProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CheckoutProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckoutProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckoutProduct[]    findAll()
 * @method CheckoutProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckoutProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CheckoutProduct::class);
    }

    // /**
    //  * @return CheckoutProduct[] Returns an array of CheckoutProduct objects
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
    public function findOneBySomeField($value): ?CheckoutProduct
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
