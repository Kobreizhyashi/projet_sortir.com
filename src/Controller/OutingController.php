<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Outing;
use App\Entity\User;
use App\Form\OutingType;
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
        $repo = $em->getRepository(Outing::class);
        $outings = $repo->findAll();

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'OutingController', 'outings' => $outings
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function createOuting(EntityManagerInterface $em, Request $request)
    {

        $outing = new Outing();
        $outing->setEtat($em->getRepository(Etat::class)->find(1));
        $outing->setOrganisateur($em->getRepository(User::class)->find(1));
        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $em->persist($outing);
            $em->flush();

            $this->addFlash('success', 'Votre sortie est en ligne ! Espérons que vous ne serez pas seul !');
            return $this->redirectToRoute("main");

        }
        return $this->render('sortie/add.html.twig', ["outingForm" => $outingForm->createView()]);
    }

    /**
     * @Route("/add/ajax_request", name="ajaxFormAdd")
     */
    public function ajaxAction(Request $request, EntityManagerInterface $em)
    {
        $choice = $request->request->get('choice');
        $lieux = $em->getRepository(Lieu::class)->findBy(array('ville' => $choice));
        dump($lieux);
        $returned = [];
        foreach ($lieux as $lieu) {
            $returned[] = $lieu;
        };

        var_dump($returned);
        $response = new Response(json_encode(array(
            'lieux'=>$returned)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
