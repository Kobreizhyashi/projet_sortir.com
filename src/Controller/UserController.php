<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function userConnexion() {

//        $outing = new Outing();
//        $outing->setEtat("1");
//        $outingForm = $this->createForm(OutingType::class,$outing);
//
//        $outingForm->handleRequest($request);
//
//        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
//
//            $em->persist($outing);
//            $em->flush();
//
//            $this->addFlash('success', 'Votre sortie est en ligne ! Espérons que vous ne serez pas seul !');
//            return $this->redirectToRoute("main");
//
//        }
        return $this->render('user/connexion.html.twig');
    }

    /**
     * @Route("/user/{id}", name="user_details")
     */
    public function userDetails($id, EntityManagerInterface $em)
    {
    // getter l'id en application une fois qu'on a la connexion utlisateur
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('user/detail.html.twig', [
            'user'=>$user
        ]);
    }

    /**
     * @Route("/user/{id}/update", name="user_update")
     */
    public function userModify(Request $request, $id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);
        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()&&$userForm->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié');
            return $this->redirectToRoute("user_details", ['id' => $user->getId()]);
        }

        return $this->render('user/update.html.twig', ["user" => $user,
            "userForm"=> $userForm->createView()
        ]);
    }

}
