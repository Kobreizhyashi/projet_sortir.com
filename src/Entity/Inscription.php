<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InscriptionRepository")
 */
class Inscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Outing", inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $outing;

    public function getId()
    {
        return $this->id;
    }

    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(?User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getOuting()
    {
        return $this->outing;
    }

    public function setOuting(?Outing $outing)
    {
        $this->outing = $outing;

        return $this;
    }
}
