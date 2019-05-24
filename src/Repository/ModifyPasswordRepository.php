<?php

namespace App\Repository;

use App\Entity\ModifyPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModifyPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModifyPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModifyPassword[]    findAll()
 * @method ModifyPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModifyPasswordRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModifyPassword::class);
    }

    // /**
    //  * @return ModifyPassword[] Returns an array of ModifyPassword objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModifyPassword
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
