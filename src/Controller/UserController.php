<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifyPwdType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/modifypwd", name="modifyPwd")
     */
    public function modifyPwd(Request $request, UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user=$this -> getUser();
        $pwdInDB=$user-> getPassword();

          dump($pwdInDB);
        //echo ('Pwd en Base: '.$pwdInDB);

        $pwdForm = $this->createForm(ModifyPwdType::class,$user);
        $pwdForm->handleRequest($request);



        if($pwdForm->isSubmitted() && $pwdForm->isValid()) {
            $current_pwd=$pwdForm-> get("currentPassword")->getData();
            $new_pwd = $pwdForm->get("newPassword")->getData();
//            echo nl2br('Pwd en Base          : '.$pwdInDB);
//            echo nl2br('courrent Pwd récupéré : '.$current_pwd);
//            echo nl2br('new Pwd récupéré      : '.$new_pwd);

            $checkPass = $passwordEncoder->isPasswordValid($user, $current_pwd);
            if ($checkPass === true) {
                $user-> setPassword($passwordEncoder->encodePassword($user, $new_pwd));
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été mis à jour !');
                return $this->redirectToRoute('my_details');
            } else {
                $this->addFlash('error', 'Votre mot de passe actuel est erroné !');
            }
        }
        return $this->render('user/modifyPwd.html.twig',['user'=>$user, 'pwdForm'=> $pwdForm->createView()]);
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
        $userForm->remove('password');
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
