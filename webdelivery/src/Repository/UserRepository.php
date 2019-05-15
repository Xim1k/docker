<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUserByToken($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.token = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    public function findByEmail($email)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getUserByResetToken($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.reset_token = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getCountByDay($year, $month, $day)
    {
        $ql = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('YEAR(u.CreatedAt) = :year')
            ->andWhere('MONTH(u.CreatedAt) = :month')
            ->andWhere('DAY(u.CreatedAt) = :day')
            ->andWhere('u.token = :token')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->setParameter('day', $day)
            ->setParameter('token', '');
            ;
        return $ql->getQuery()->getSingleScalarResult();
    }

    public function findBySeller(int $id , $page = 1)
    {
        $query = $this->createQueryBuilder('u'          )
            ->andWhere('u.seller = :id')
            ->setParameter('id', $id)
        ;

        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function findByLoginAndRole($page = 1, string $search = null, $role = null)
    {
        if ($role == "Любая")
        {
            $role = null;
        }
        $query = $this->createQueryBuilder('u')
            ->where('u.login LIKE :search')
            ->andWhere('u.role LIKE :role')
            ->setParameters([
                'search' => '%' . $search . '%',
                'role' => '%' .$role .'%'
            ])
        ;
        $paginator = $this->paginate($query->getQuery(), $page ?: 1, 4);
        return $paginator;
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
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
