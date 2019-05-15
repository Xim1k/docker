<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Seller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
      * @return Product[] Returns an array of Product objects
      */
    public function findBySellerId($sellerId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.seller = :seller')
            ->setParameter('seller', $sellerId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
      * @return Product[] Returns an array of Product objects
      */

    public function searchProducts(?string $search, $sellerId, $page = 1, $category = null)
    {
        $query = $this->createQueryBuilder('p')
            ->Where('p.name LIKE :search')
            ->andWhere('p.seller = :seller')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('seller', $sellerId)
        ;

        if (!empty($category))
        {
            $query->andWhere('p.category = :category')
                ->setParameter('category', $category)
            ;
        }

        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function findBySeller(Seller $seller, $page = 1, $search)
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.seller = :seller')
            ->andWhere('p.name LIKE :search')
            ->setParameter('seller', $seller->getId())
            ->setParameter('search', '%' . $search . '%')
        ;
        
        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function paginate($dql, $page = 1, $limit = 9)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
        return $paginator;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.seller = :seller')
            ->setParameter('seller', $seller)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById($id): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
