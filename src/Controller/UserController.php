<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

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
