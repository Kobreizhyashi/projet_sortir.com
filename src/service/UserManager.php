<?php


namespace App\service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     * Adapter l'affichage du profil en fonction de si c'est celui de l'utilisateur courant ou non
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){

        $this->entityManager = $entityManager;
    }

    public function filterUsersToDetails($idUserToView, User $user){

        $pictureParams = $this->isPicture($user);

        $result = ['their_details', ['user'=>$user]];
        if($user->getId()==$idUserToView)
            $result = ['my_details', ['user'=>$user]];
        return $result;
    }


    public function isPicture(User $user){
        $picturePath = $user->getPicturePath();
        //générer un booléen permettant de ne pas afficher l'image si elle n'existe pas
        $isPicture = true;
        if($picturePath == 'uploads/pictures/'){
            $isPicture = false;
        }
        $result = ['user'=>$user, 'picturePath'=>$picturePath, 'picture'=>$isPicture];
        return $result;
    }


}