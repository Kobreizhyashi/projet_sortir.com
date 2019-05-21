<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtatRepository")
 */
class Etat
{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="etat")
     */
    private $outings;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOutings()
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing)
    {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setEtat($this);
        }

        return $this;
    }
    

    public function removeOuting(Outing $outing)
    {
        if ($this->outings->contains($outing)) {
            $this->outings->removeElement($outing);
            // set the owning side to null (unless already changed)
            if ($outing->getEtat() === $this) {
                $outing->setEtat(null);
            }
        }

        return $this;
    }
}
