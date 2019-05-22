<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{

    /**
     * on nomme la route login car dans le fichier
     * security.yaml on a login_path: login
     * @Route("/login", name="login")
     */
    public function login(){
        return $this->render("user/login.html.twig",
            []);
    }

    /**
     * Symfony gére entierement cette route il suffit de l'appeler logout.
     * Penser à parametre le fichier security.yaml pour rediriger la déconnexion.
     * @Route("/logout", name="logout")
     */
    public function logout(){}

    /**
     * @Route("/user", name="user")
     */


    /**
     * @Route("/user/{id}", name="user_details")
     */
    public function userDetails($id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);

        return $this->render('user/detail.html.twig', [
            'user'=>$user
        ]);
    }

}
