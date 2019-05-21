<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Outing;
use App\Form\OutingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OutingController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('sortie/connexion.html.twig', [
            'controller_name' => 'OutingController',
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function createOuting(EntityManagerInterface $em, Request $request) {

        $outing = new Outing();
        $outing->setEtat($em->getRepository(Etat::class)->find(1));
        $outingForm = $this->createForm(OutingType::class,$outing);

        $outingForm->handleRequest($request);

        if ($outingForm->isSubmitted() && $outingForm->isValid()) {

            $em->persist($outing);
            $em->flush();

            $this->addFlash('success', 'Votre sortie est en ligne ! Espérons que vous ne serez pas seul !');
            return $this->redirectToRoute("main");

        }
        return $this->render('sortie/add.html.twig', ["outingForm"=> $outingForm->createView()]);
    }
}
