<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Merci de télécharger un fichier image")
     * @Assert\File(mimeTypes={ "image/jpeg" , "image/png" })
     */
    private $img;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="picture", cascade={"persist", "remove"})
     */
    private $user;

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newPicture = $user === null ? null : $this;
        if ($newPicture !== $user->getPicture()) {
            $user->setPicture($newPicture);
        }

        return $this;
    }

}
