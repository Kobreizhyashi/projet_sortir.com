<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FolderRepository")
 */
class Folder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     *
     * @Assert\NotBlank(message="Veuillez charger un fichier !")
     * @Assert\File(
     *        mimeTypes={"application/csvm+json"},
     *        mimeTypesMessage = "Veuillez joindre un fichier de type csv ...",
     *        maxSize = "4M",
     *        maxSizeMessage = "Le fichier à joindre est trop volumineux !"
     * )
     */
    private $File;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->File;
    }

    public function setFile(?string $File): self
    {
        $this->File = $File;

        return $this;
    }
}
