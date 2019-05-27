<?php

namespace App\Repository;

use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\User;
use App\Form\DeleteOutingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    // /**
    //  * @return Outing[] Returns an array of Outing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Outing
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function removeOuting($idToRemove){
        $em = $this->getEntityManager();
        $dql = "DELETE o
                FROM App\Entity\Outing o
                WHERE o.id = $idToRemove";

        $query = $em->createQuery($dql);
        $result = $query->getResult();
        return $result;
    }


    public function getPersonalResearch($requestedArray, EntityManagerInterface $em)
    {


        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.etat', 'etat')
            ->leftJoin('o.site', 's')
            ->leftJoin('o.inscriptions', 'i')
            ->where('1 = 1');


        if ($requestedArray['siteValue'] != '' && $requestedArray['siteValue'] != NULL && $requestedArray['siteValue'] != 131) {
            $qb->andWhere('s.id = :site')
                ->setParameter('site', $requestedArray['siteValue']);
        }

        $qb->andWhere('o.nom like :nom')
            ->setParameter('nom', '%' . $requestedArray['stringSearch'] . '%');

        if ($requestedArray['dateFirst'] != '' && $requestedArray['dateFirst'] != NULL) {
            $qb->andWhere('o.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $requestedArray['dateFirst']);
        };

        if ($requestedArray['dateLast'] != '' && $requestedArray['dateLast'] != NULL) {
            $qb->andWhere('o.dateHeureDebut <= :dateMax')
                ->setParameter('dateMax', $requestedArray['dateLast']);
        };

        if ($requestedArray['isOrganizer'] == true) {
            $qb->andWhere('o.organisateur = :currentUser')
                ->setParameter('currentUser', $requestedArray['currentUserID']);
        }
        if ($requestedArray['isInscrit'] == true) {
                $qb->andWhere('i.user = :currentUser')
                /// $qb->join('s.users', 'u', 'WITH', 'u.id = :currentUser')
                ->setParameter('currentUser', $requestedArray['currentUserID']);
        }



        $query = $qb->getQuery();
        $returned = $query->getResult();


        dump($returned);
        /*



        =================== A GARDER ==========================

        $sqlString = '
        SELECT o FROM App\Entity\Outing o 
        JOIN App\Entity\Site s
        JOIN App\Entity\Etat e
        WHERE 1 = 1 ';
        $parametersArray = [];

        if ($requestedArray['siteValue'] != 131) {
            $sqlString .= 'AND s.id = :siteValue ';
            $parametersArray['siteValue'] = $requestedArray['siteValue'];
        }
        if (($requestedArray['dateFirst'] != '') && ($requestedArray['dateLast'] != '')) {
            $sqlString .= 'AND o.dateHeureDebut > :dateFirst AND o.dateHeureDebut < :dateLast ';
            $parametersArray['dateFirst'] = $requestedArray['dateFirst'];
            $parametersArray['dateLast'] = $requestedArray['dateLast'];
        } elseif (($requestedArray['dateFirst'] != '')) {
            $sqlString .= 'AND o.dateHeureDebut > :dateFirst ';
            $parametersArray['dateFirst'] = $requestedArray['dateFirst'];
        } elseif (($requestedArray['dateLast'] != '')) {
            $sqlString .= 'AND o.dateHeureDebut > :dateFirst ';
            $parametersArray['dateLast'] = $requestedArray['dateLast'];
        }
        if ($requestedArray['stringSearch'] != '') {

            $sqlString .= 'AND o.nom LIKE :stringSearch';
            $parametersArray['stringSearch'] = $requestedArray['stringSearch'];
        }

        $query = $this->getEntityManager()->createQuery($sqlString);

        if (!empty($parametersArray['siteValue'])) {
            $query->setParameter('siteValue', $parametersArray['siteValue']);
        }
        if ((!empty($parametersArray['dateFirst'])) && (!empty($parametersArray['dateLast']))) {
            $query->setParameter('dateFirst', '%' . $parametersArray['dateFirst'] . '%');
            $query->setParameter('dateLast', '%' . $parametersArray['dateLast'] . '%');
        } elseif (!empty($parametersArray['dateFirst'])) {
            $query->setParameter('dateFirst', '%' . $parametersArray['dateFirst'] . '%');
        } elseif (!empty($parametersArray['dateLast'])) {
            $query->setParameter('dateLast', '%' . $parametersArray['dateLast'] . '%');
        }
        if (!empty($parametersArray['stringSearch'])) {
            $query->setParameter('dateLast', '%' . $parametersArray['stringSearch'] . '%');
        }

        $returned = $query->getResult();

*/


        /** @var Outing $outing */
        foreach ($returned as $outing) {
            $returned[$outing->getId()] = [
                'nom' => $outing->getNom(),
                'dateHeureDebut' => $outing->getDateHeureDebut()->format('d-m-Y'),
                'duree' => $outing->getDuree(),
                'dateLimiteInscription' => $outing->getDateLimiteInscription()->format('d-m-Y'),
                'nbInscriptions' => $outing->getInscriptions()->count(),
                'nbInscriptionsMax' => $outing->getNbInscriptionsMax(),
                'infosSortie' => $outing->getInfosSortie(),
                'etat' => $outing->getEtat()->getLibelle(),
                'organizerName' => $outing->getOrganisateur()->getUsername()
            ];
        };
        return $returned;


    }


}
