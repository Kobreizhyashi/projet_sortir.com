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

//    /**
//     * @ORM\Column(type="string", length=255, nullable=true)
//     */
    //private $path;

    public function getId()
    {
        return $this->id;
    }

//    public function getPath()
//    {
//        return $this->path;
//    }
//
//    public function setPath(?string $path)
//    {
//        $this->path = $path;
//        return $this;
//    }

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please, upload the product brochure as a PDF file.")
     * @Assert\File(mimeTypes={ "image/jpeg" })
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
