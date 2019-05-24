<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Outing;
use App\Entity\Site;
use App\Entity\User;
use App\Form\OutingDeleteType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class OutingController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index(EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userId = $this->getUser()->getId();

        $repo = $em->getRepository(Outing::class);
        $outings = $repo->findAll();

        $repo = $em->getRepository(Site::class);
        $sites = $repo->findAll();
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'OutingController', 'outings' => $outings, 'sites' => $sites, 'userId'=>$userId
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function createOuting(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $userId = $this->getUser()->getId();






        $outing = new Outing();
        $outing->setEtat($em->getRepository(Etat::class)->find(1));
        $outing->setOrganisateur($em->getRepository(User::class)->find($userId));
        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $em->persist($outing);
            $em->flush();
            $em->getRepository(Inscription::class)->subscribeManager($outing, $this->getUser(), $em);


            $this->addFlash('success', 'Votre sortie est en ligne ! Espérons que vous ne serez pas seul !');
            return $this->redirectToRoute("main");

        }
        return $this->render('sortie/add.html.twig', ["outingForm" => $outingForm->createView()]);
    }


    /**
     * @Route("/show/{id}", name="show",requirements={"id":"\d+"})
     */
    public function showOuting($id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $OutingRepo=$this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $InscriptionRepo=$this->getDoctrine()->getRepository(Inscription::class);
        $Inscription= $InscriptionRepo->findBy(array('outing'=>$id));

        if(empty($Outing)){
            throw $this->createNotFoundException("This outing do not exists !");
        }

        return $this->render('sortie/afficher_sortie.html.twig', array("outing"=>$Outing,"users"=>$Inscription));
    }

    /**
     * @Route("/update/{id}", name="update",requirements={"id":"\d+"})
     */
    public function update(Request $request, $id,EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $OutingRepo=$this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $outingForm = $this->createForm(OutingType::class, $Outing);
        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $em->persist($Outing);
            $em->flush();

            $this->addFlash('success', 'Votre sortie est en ligne ! Espérons que vous ne serez pas seul !');
            return $this->redirectToRoute("main");

        }

        return $this->render('sortie/update.html.twig', ["outingForm" => $outingForm->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="delete",requirements={"id":"\d+"})
     */
    public function delete($id,Request $request,EntityManagerInterface $em)
    {

        $OutingRepo=$this->getDoctrine()->getRepository(Outing::class);
        $Outing = $OutingRepo->find($id);

        $EtatRepo=$this->getDoctrine()->getRepository(Etat::class);
        $Etat = $EtatRepo->find(6);

        if(empty($Etat)){
            throw $this->createNotFoundException("This etat do not exists !");
        }

        if(empty($Outing)){
            throw $this->createNotFoundException("This outing do not exists !");
        }

        $outingForm = $this->createForm(OutingDeleteType::class,$Outing);
        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            $Outing->setEtat($Etat);
            $em->persist($Outing);
            $em->flush();

            $this->addFlash('success', 'Votre sortie est bien supprimée!');
            return $this->redirectToRoute("main");

        }

        return $this->render('sortie/annuler_sortie.html.twig',["outing"=>$Outing,'outingForm'=>$outingForm->createView()]);
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



        dump($returned);

        $response = new Response(json_encode($returned));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
