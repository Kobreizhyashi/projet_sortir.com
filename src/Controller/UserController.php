<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\Site;
use App\Entity\Upload;
use App\Entity\User;
use App\Form\ModifyPwdType;
use App\Form\UploadType;
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
     * fichier CSV doit contenir liste de users avec prenom, nom et email
     * ce fichier sera envoyé en base avec email généré sous format prenom.nom.random(1000,9999)
     * ce fichier est enregistré sous public/userdata en crypté sha1
     * @Route("/insertfile", name="insert_file")
     */
    public function insertFile(Request $request, UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $em) {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $upload =new Upload();
        $fileForm = $this->createForm(UploadType::class,$upload);
        $fileForm->handleRequest($request);

        if($fileForm->isSubmitted() && $fileForm->isValid()) {
            $file = $upload->getName();

            foreach (file($file) as $line) {
                $userdata[]=explode(',',$line);
                $user = new User();
                //Affectation par défaut sur site de Nantes
                $repo = $em->getRepository(Site::class);
                $siteNantes = $repo->find('2');
                $user->setSite($siteNantes);
                $user->setNom($userdata[0][1]);
                $user->setPrenom($userdata[0][0]);
                $user->setAdministrateur(0);
                $user->setActif(1);
                $user->setUsername($userdata[0][0].$userdata[0][1]);
                $user->setEmail(rtrim($userdata[0][2]));
                $password = $userdata[0][0].$userdata[0][1].random_int(1000,9999);
                $user-> setPassword($passwordEncoder->encodePassword($user,$password));
                unset($userdata);

                $em->persist($user);
                $em->flush();

            }

            //fichier en crypté
            $fileName = sha1(uniqid()).'.'.$file->guessExtension();

            $file->move($this->getParameter('userdata_directory'),$fileName);
            $upload->setName($fileName);

            $this->addFlash('success', 'Votre fichier a bien été chargé sous le répertoire -uploads- !');

            return $this->redirectToRoute('my_details');

        }

        return $this->render('user/insertFile.html.twig',['fileForm'=> $fileForm->createView()]);
    }


    /**
     * on nomme la route login car dans le fichier
     * security.yaml on a login_path: login
     * @Route("/login", name="login")
     */
    public function login(EntityManagerInterface $em, AuthenticationUtils $authenticationUtils, TranslatorInterface  $translator){
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
     * @Route("/modifypwd", name="modifyPwd")
     */
    public function modifyPwd(Request $request, UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user=$this -> getUser();
        $pwdInDB=$user-> getPassword();

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
     * @Route("/getprofile/{id}", name="get_profile", requirements={"id"="\d+"})
     * routing pour visionnage infos profil
     */
    public function getProfile(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $connectedUser = $this->getUser();

        if($connectedUser->getId()==$id){
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

    /**
     * @Route("/user/create", name="user_create")
     * Creer manuellement un profil
     */
    public function createUser(Request $request, EntityManagerInterface $em,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');


        $user = new User();

        $userForm = $this->createForm(UserType::class,$user);

        $user->setAdministrateur(0);
        $user->setActif(1);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {


            $user->setAdministrateur(0);

            $new_pwd = $userForm->get("password")->getData();
            $user-> setPassword($passwordEncoder->encodePassword($user, $new_pwd));
            $user->setActif(1);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été créer !');
            return $this->redirectToRoute("login");

        }


        return $this->render('user/createManually.html.twig', ["userForm"=> $userForm->createView()]);
    }

}
