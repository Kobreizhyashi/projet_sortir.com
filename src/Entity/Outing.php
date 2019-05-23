<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutingRepository")
 */
class Outing
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
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $infosSortie;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inscription", mappedBy="outing")
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $motif;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
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

    public function getDateHeureDebut()
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut)
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree()
    {
        return $this->duree;
    }

    public function setDuree(?int $duree)
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription()
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription)
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax()
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax)
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie()
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie)
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }



    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions()
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription)
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setOuting($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription)
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getOuting() === $this) {
                $inscription->setOuting(null);
            }
        }

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite(?Site $site)
    {
        $this->site = $site;

        return $this;
    }

    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur)
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function setEtat(Etat $etat)
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu()
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
