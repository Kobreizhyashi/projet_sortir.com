<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LieuRepository")
 */
class Lieu
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $rue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="lieu")
     */
    private $outings;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville", inversedBy="lieus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue()
    {
        return $this->rue;
    }

    public function setRue(?string $rue)
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing)
    {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setLieu($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing)
    {
        if ($this->outings->contains($outing)) {
            $this->outings->removeElement($outing);
            // set the owning side to null (unless already changed)
            if ($outing->getLieu() === $this) {
                $outing->setLieu(null);
            }
        }

        return $this;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville)
    {
        $this->ville = $ville;

        return $this;
    }
}
