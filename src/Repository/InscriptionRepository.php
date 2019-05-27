<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    // /**
    //  * @return Inscription[] Returns an array of Inscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Inscription
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

public function subscribeManager($outing, $user, EntityManagerInterface $em) {

    $subscribe = new Inscription();
    $subscribe->setDateInscription(new \DateTime());
    $subscribe->setOuting($outing);
    $subscribe->setUser($user);
    $em->persist($subscribe);
    $em->flush();

}

public function renounceManager($outing, $user, EntityManagerInterface $em) {

    $subscribe = new Inscription();
    $subscribe->setDateInscription(new \DateTime());
    $subscribe->setOuting($outing);
    $subscribe->setUser($user);
    $em->remove($subscribe);
    $em->flush();

}


public function getInscrit($outing, $userId) {


    $qb = $this->createQueryBuilder('i')
        ->leftJoin('i.user', 'u')
        ->leftJoin('i.outing', 'o')
        ->where('u.id = :user')
        ->setParameter('user', $userId)
        ->andWhere('o.id = :outingId')
        ->setParameter('outingId', $outing->getId());


// Si c'est vide, c'est good, pas d'inscription, sinon, c'est inscrit.
    $query = $qb->getQuery();
    $returned = $query->getResult();

    if (count($returned) === 1) {
        return true;
    } else {
        return false;
    }





}


}
