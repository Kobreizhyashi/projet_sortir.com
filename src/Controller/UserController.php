<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Bundle\FixturesBundle;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends Controller
{

    /**
     * on nomme la route login car dans le fichier
     * security.yaml on a login_path: login
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface  $translator){

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if (!empty($error)) {
          $this->addFlash('error', $translator->trans($error->getMessageKey(), [], 'security'));
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("user/login.html.twig",[
            'last_username' => $lastUsername,
            'error'         => $error,
            ]);
    }

    /**
     * Symfony gére entierement cette route il suffit de l'appeler logout.
     * Penser à parametre le fichier security.yaml pour rediriger la déconnexion.
     * @Route("/logout", name="logout")
     */
    public function logout(){}


    /**
     * @Route("/myprofile", name="my_details")
     * voir les informations de son propre profil
     */
    public function myDetails(EntityManagerInterface $em)
    {
       $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        return $this->render('user/detail.html.twig', [
            'user'=>$user
        ]);
    }

    /**
     * @Route("/user/{id}", name="their_details", requirements={"id"="\d+"})
     * voir les informations d'un autre profil
     */
    public function theirDetails(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $em->getRepository(User::class)->find($id);
        return $this->render('user/detail.html.twig', [
            'user'=>$user
        ]);
    }


    /**
     * @Route("/user/update", name="user_update")
     * Mettre à jour ses informations de profil
     */
    public function userUpdate(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()&&$userForm->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié');
            return $this->redirectToRoute("my_details", ['user' => $user]);
        }

        return $this->render('user/update.html.twig', ["user" => $user,
            "userForm"=> $userForm->createView()
        ]);
    }

}
