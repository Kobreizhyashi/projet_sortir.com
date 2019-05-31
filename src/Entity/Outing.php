<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Veuillez saisir un nom de sortie")
     * @Assert\Length(max="60",maxMessage="Le nom ne doit pas dépasser 60 caractères")
     * @ORM\Column(type="string", length=60)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\Expression(
     *     "this.checkBeginningDate()==false",
     *     message="La date de début de l'événement ne peut être antérieure à la date actuelle"
     * )

     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer", precision=4, nullable=true)
     * @Assert\LessThan(value = 10081,
     *     message="Alors, on se la coule douce ? Faudrait penser à bosser de temps en temps !")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Expression(
     *     "this.compareDates()==true",
     *     message="Saisir une date limite d'inscription antérieure à la date de début de l'événement"
     * )
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer", precision=3, nullable=true)
     * @Assert\LessThan(value = 100,
     *     message="C'est plus une sortie à ce niveau-là ! Veuillez indiquer un nombre d'inscriptions inférieur à 100")
     */
    private $nbInscriptionsMax;

    /**
     * @Assert\Length(max="500",maxMessage="Les informtations ne doivent pas dépasser 500 caractères")
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
     * @Assert\Length(max="1000",maxMessage="Le motif ne doit pas dépasser 1000 caractères")
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

    //Vérification que le date de début de l'événement n'est pas antérieure à maintenant
    public function checkBeginningDate(){
        if($this->getDateHeureDebut()> new \DateTime('now')){
            return false;
        } else {
            return true;
        }
    }

    //    Comparaison de la date limite d'nscription avec la date de début de sortie
    public function compareDates(){
        if($this->getDateHeureDebut()<$this->getDateLimiteInscription()){
            return false;
        } else {
            return true;
        }
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
