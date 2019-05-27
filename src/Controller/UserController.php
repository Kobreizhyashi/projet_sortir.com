<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\User;
use App\Form\ModifyPwdType;
use App\Form\PictureType;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use App\service\FileUploader;

class UserController extends Controller
{

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

        // dump ne fonctionne pas !!!
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
                return $this->render('user/modifyPwd.html.twig',
                    ["user" => $user,
                        "pwdForm" => $pwdForm->createView()]);
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



    //ITERATION 2


    /**
     * @Route("/picture", name="user_picture")
     * Upload de la photo de profil
     */
    public function uploadPicture(Request $request, EntityManagerInterface $em){

        $this->denyAccessUnlessGranted('ROLE_USER');
        $picture = new Picture();
        $pictureForm = $this->createForm(PictureType::class,$picture);
        $pictureForm->handleRequest($request)->getData();
        if($pictureForm->isSubmitted()&&$pictureForm->isValid()) {




            // $file stores the uploaded file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

            $file = $picture->getImg();

            //Nouvelle méthode. fonctionne-t-elle ?
            $fileName = $fileUploader->upload($file);
            $product->setImg($fileName);


            //Ancienne méthode qui fonctionne : !!!!!!!
//            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
//            try {
//                $file->move(
//                    $this->getParameter('pictures_directory'),
//                    $fileName
//                );
//            } catch (FileException $e) {
//                // ... handle exception if something happens during file upload
//                echo('echec de la mise dans le dossier du picture : ' . $e);
//            }
//
//            // updates the 'picture' property to store the file name
//            // instead of its contents
//            $picture->setImg($fileName);

            // ... persist the $product variable or any other work

            dump('test1');
            $em->persist($picture);
            $em->flush();

            $this->addFlash('success', 'Votre photo a bien été téléchargée');
            return $this->redirectToRoute("my_details", ['user' => $picture]);
        }

        return $this->render('user/picture.html.twig', ["picture" => $picture,
            "pictureForm"=> $pictureForm->createView()
        ]);

    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

}
