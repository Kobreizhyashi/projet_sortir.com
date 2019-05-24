<?php

namespace App\Controller;

use App\Entity\ModifyPassword;
use App\Form\ModifyPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Bundle\FixturesBundle;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Route("/modifypassword", name="modifyPassword")
     */
    public function change_user_password(Request $request, UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $em)
    {
        $user=$this -> getUser();
        dump($user);

        $pwdForm = $this->createForm(ModifyPasswordType::class,$user);
        $pwdForm->handleRequest($request);


//        if($pwdForm->isSubmitted()&& $pwdForm->isValid()) {
//            $old_pwd = $request->get('old_password');
//            $new_pwd = $request->get('new_password');
//            $new_pwd_confirm = $request->get('new_password_confirm');
//
//
//
//            $em->persist($user);
//            $em->flush();
//
//            $this->addFlash('success', 'Votre profil a bien été modifié');
//            return $this->redirectToRoute("my_details", ['user' => $user]);
//        }
//
//        $old_pwd = $request->get('old_password');
//        $new_pwd = $request->get('new_password');
//        $new_pwd_confirm = $request->get('new_password_confirm');
//        $user = $this->getUser();
//        $checkPass = $passwordEncoder->isPasswordValid($user, $old_pwd);
//        if($checkPass === true) {
//            return $this->render('user/modifyPassword.html.twig',
//                ["user" => $user,
//                "pwdForm"=> $pwdForm->createView()
//            ]);
//        } else {
//            $this->addFlash('error', 'Votre mot de passe actuel est erronné !');
//            return $this->render('user/modifyPassword.html.twig', ['user'=>$user]);
//        }
        return $this->render("user/modifyPassword.html.twig",[]);
    }

//    public function userUpdate(Request $request, EntityManagerInterface $em)
//    {
//        $this->denyAccessUnlessGranted('ROLE_USER');
//
//        $user = $this->getUser();
//
//        $userForm = $this->createForm(UserType::class,$user);
//        $userForm->handleRequest($request);
//
//        if($userForm->isSubmitted()&&$userForm->isValid()) {
//            $em->persist($user);
//            $em->flush();
//
//            $this->addFlash('success', 'Votre profil a bien été modifié');
//            return $this->redirectToRoute("my_details", ['user' => $user]);
//        }
//
//        return $this->render('user/update.html.twig', ["user" => $user,
//            "userForm"=> $userForm->createView()
//        ]);
//    }


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
     * @Route("/getprofile/{id}", name="get_profile", requirements={"id"="\d+"})
     * routing pour visionnage infos profil
     */
    public function getProfile(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $connectedUser = $this->getUser();
        $connectedUserId = $connectedUser->getId();

        $toViewUser = $em->getRepository(User::class)->find($id);

        if($connectedUserId==$id){
            return $this->redirectToRoute('my_details', [
                'user'=>$connectedUser
            ]);
        } else {
            return $this->redirectToRoute('their_details', [
                'id'=>$id
            ]);
        }

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
