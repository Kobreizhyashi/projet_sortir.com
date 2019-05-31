<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Outing;
use App\Entity\Site;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\OutingDeleteType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;


class OutingController extends Controller
{
    /**
     * @Route("/admin/sortieadmin", name="admin_sortie")
     *
     **/
    public function outingUpdateAdmin(EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->getUser();
        $repo = $em->getRepository(Outing::class);
        $outings = $repo->findAll();
        $repo = $em->getRepository(Site::class);
        $sites = $repo->findAll();

        return $this->render('sortie/sortieadmin.html.twig', [
            'controller_name' => 'OutingController', 'outings' => $outings, 'sites' => $sites, 'user'=>$user
        ]);
    }


    /**
     * @Route("/", name="main")
     */
    public function index(EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $site = $em->getRepository(Site::class)->find($this->getUser()->getSite());


        $repo = $em->getRepository(Outing::class);
        $outings = $repo->findAll();

        //Update des états
        foreach ($outings as $outing) {
            $this->updateEtats($outing);
        }

        //Gestion des états
        $repoEtat = $em->getRepository(Etat::class);
        $etats = [];
        for ($i=2; $i<=6; $i++){
           $etats[] = $repoEtat->find($i);
        }
        $outings = $repo->findBy(array('site' => $site, 'etat' => $etats));

        $repo = $em->getRepository(Site::class);
        $sites = $repo->findAll();

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'OutingController', 'outings' => $outings, 'sites' => $sites, 'user' => $user, 'site' => $site
        ]);
    }


    /**
     * @Route("/subscribe/{id}", name="subscribe")
     */
    public function subscribe(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $OutingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $em->getRepository(Inscription::class)->subscribeManager($Outing, $this->getUser(), $em);

        $this->addFlash('success', 'Votre inscription est validée ! Espérons que vous ne serez pas seul !');
        return $this->redirectToRoute("main");

    }

    /**
     * @Route("/renounce/{id}", name="renounce")
     */
    public function renounce(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $InscriptionRepo = $this->getDoctrine()->getRepository(Inscription::class);
        $Inscription = $InscriptionRepo->findOneBy(array('outing' => $id, 'user' => $this->getUser()->getId()));

        if (empty($Inscription)) {
            throw $this->createNotFoundException("This outing do not exists !");
        }
        $em->remove($Inscription);
        $em->flush();

        $this->addFlash('success', 'Votre désistement est validé !');
        return $this->redirectToRoute("main");

    }

    /**
     * @Route("/publish/{id}", name="publish")
     */
    public function publish(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $outing=$em->getRepository(Outing::class)->find($id);

        $ouverte = $this->getDoctrine()
            ->getRepository(Etat::class)
            ->find(2);

        $outing->setEtat($ouverte);
        $em->persist($outing);
        $em->flush();

        $this->addFlash('success', 'Votre sortie est publiée !');
        return $this->redirectToRoute("main");

    }



    /**
     * @Route("/add", name="add")
     */
    public function createOuting(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userId = $this->getUser()->getId();

        // Liste des villes
        $repo = $em->getRepository(Ville::class);
        $villes = $repo->findAll();

        $dateDebut = new \DateTime('now');
        $dateDebut->add(new \DateInterval('PT' . 1440 . 'M'));
        $dateDebut->setTime(18, 0);

        $outing = new Outing();
        $outing->setDateHeureDebut($dateDebut);
        $outing->setDateLimiteInscription($dateDebut);
        $outing->setEtat($em->getRepository(Etat::class)->find(1));
        $outing->setOrganisateur($em->getRepository(User::class)->find($userId));
        $outing->setSite($em->getRepository(Site::class)->find($this->getUser()->getSite()));
        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request)->getData();

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $em->persist($outing);
            $em->flush();
            $em->getRepository(Inscription::class)->subscribeManager($outing, $this->getUser(), $em);

            $this->addFlash('success', 'Votre sortie est en ligne ! Espérons que vous ne serez pas seul !');

            //attention aux cas où plusieurs noms sont identiques !
            return $this->redirectToRoute("show", ['id'=> $outing->getId()]);
        }
        return $this->render('sortie/add.html.twig', ["outingForm" => $outingForm->createView(), "villes" => $villes]);
    }


    /**
     * @Route("/show/{id}", name="show",requirements={"id":"\d+"})
     */
    public function showOuting($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userId = $this->getUser()->getId();

        $OutingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $InscriptionRepo = $this->getDoctrine()->getRepository(Inscription::class);
        $Inscription = $InscriptionRepo->findBy(array('outing' => $id));

        if (empty($Outing)) {
            throw $this->createNotFoundException("This outing do not exists !");
        }

        return $this->render('sortie/afficher_sortie.html.twig', array("outing" => $Outing, "users" => $Inscription, "userId" => $userId));
    }

    /**
     * @Route("/update/{id}", name="update",requirements={"id":"\d+"})
     */
    public function update(Request $request, $id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $OutingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $outingForm = $this->createForm(OutingType::class, $Outing);
        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $em->persist($Outing);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a bien été modifiée !');
            return $this->redirectToRoute("show", ['id'=>$id]);

        }

        return $this->render('sortie/update.html.twig', ["outingForm" => $outingForm->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="delete",requirements={"id":"\d+"})
     */
    public function delete($id, Request $request, EntityManagerInterface $em)
    {

        $OutingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $EtatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $Etat = $EtatRepo->find(6);

        if (empty($Etat)) {
            throw $this->createNotFoundException("This etat do not exists !");
        }

        if (empty($Outing)) {
            throw $this->createNotFoundException("This outing do not exists !");
        }

        $outingForm = $this->createForm(OutingDeleteType::class, $Outing);
        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            $Outing->setEtat($Etat);
            $em->persist($Outing);
            $em->flush();

            $this->addFlash('success', 'Votre sortie est bien supprimée!');
            return $this->redirectToRoute("main");

        }

        return $this->render('sortie/annuler_sortie.html.twig', ["outing" => $Outing, 'outingForm' => $outingForm->createView()]);
    }

    /**
     * Mise à jour des états appelée à chaque affichage de 'main'
     */
    public function updateEtats($outing)
    {
        $now = new \DateTime('now');
        $duree = $outing->getDuree();
        $debut = $outing->getDateHeureDebut();
        $dateLimiteInscription = $outing->getDateLimiteInscription();

        $clone = clone $outing->getDateHeureDebut();

        $fin = $clone->add(new \DateInterval('PT' . $duree . 'M'));

        $repoEtat = $this->getDoctrine()->getRepository(Etat::class);
        $creee = $repoEtat->find(1);
        $ouverte = $repoEtat->find(2);
        $cloturee = $repoEtat->find(3);
        $enCours = $repoEtat->find(4);
        $passee = $repoEtat->find(5);
        $annulee = $repoEtat->find(6);

        $archiveThreshold = 43200;

        if ($outing->getEtat() != $annulee && $outing->getEtat() != $creee) {
            if ($now > $debut && $now < $fin) {
                $outing->setEtat($enCours);
                //ajout er elseif pour cloture
            } elseif ($now > $dateLimiteInscription && $now < $debut) {
                $outing->setEtat($cloturee);
            } elseif ($now < $debut) {
                $outing->setEtat($ouverte);
            } elseif ($now > $fin) {
                $cloneFin = clone $fin;
                $cloneFin->add(new \DateInterval('PT' . $archiveThreshold . 'M'));
                if ($now > $cloneFin) {
                    $this->archive($outing);
                } else {
                    $outing->setEtat($passee);
                }
            }
        }
        $this->redirectToRoute('main');
    }

    //Attention, la suppression se fait manifestement après l'affichage
    public function archive($outing)
    {
        echo('suppression');
        var_dump($outing->getNom());
        $outingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $outingRepo->removeOuting($outing);
    }


    /**
     * @Route("/add/ajax_request", name="ajaxFormAdd")
     */
    public function ajaxAction(Request $request, EntityManagerInterface $em)
    {
        $choice = $request->request->get('choice');
        $lieux = $em->getRepository(Lieu::class)->findBy(array('ville' => $choice));

        $returned = [];
        foreach ($lieux as $lieu) {
            $returned[$lieu->getId()] = $lieu->getNom();
        };

        $response = new Response(json_encode($returned));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @Route("/ajaxFormIndex", name="ajaxFormIndex")
     */
    public function ajaxFormIndex(Request $request, EntityManagerInterface $em)
    {
        //Update des états
        $repo = $em->getRepository(Outing::class);
        $outings = $repo->findAll();
        foreach ($outings as $outing) {
            $this->updateEtats($outing);
        }

        $requestedArray['siteValue'] = $request->request->get('siteValue');
        $requestedArray['dateFirst'] = $request->request->get('dateFirst');
        $requestedArray['dateLast'] = $request->request->get('dateLast');
        $requestedArray['stringSearch'] = $request->request->get('stringSearch');
        $requestedArray['isOrganizer'] = $request->request->get('isOrganizer');
        $requestedArray['isInscrit'] = $request->request->get('isInscrit');
        $requestedArray['isNotInscrit'] = $request->request->get('isNotInscrit');
        $requestedArray['finishedOutings'] = $request->request->get('finishedOutings');
        $requestedArray['currentUserID'] = $this->getUser()->getId();

        $returned = $em->getRepository(Outing::class)->getPersonalResearch($requestedArray, $em);


        $response = new Response(json_encode($returned));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/ajaxSiteAdd", name="ajaxSiteAdd")
     */
    public function ajaxSiteAdd(Request $req, EntityManagerInterface $em)
    {


        if ($req->request->get('ville') != '' && $req->request->get('cpo') != '') {
            $ville = new Ville();
            $ville->setNom($req->request->get('ville'));
            $ville->setCodePostal($req->request->get('cpo'));

            $em->getRepository(Ville::class)->VilleCreationManager($ville, $em);
            $em->getRepository(Lieu::class)->LieuCreationManagerWithNewCity($ville, $req, $em);

            $ville = $em->getRepository(Ville::class)->find($ville);

            $return = [
                'id' => $ville->getId(),
                'nom' => $ville->getNom()
            ];

            $response = new Response(json_encode($return));

        } else {
            $em->getRepository(Lieu::class)->LieuCreationManager($req, $em);
            $response = new Response();
        }


        return $response;

    }


}
