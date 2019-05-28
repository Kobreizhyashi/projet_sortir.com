<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Inscription;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Picture;

/**
 * @UniqueEntity(fields={"username"})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    public function getRoles()
    {
        return ["ROLE_USER", "ROLE_ADMIN"];

    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Veuillez saisir votre nom")
     * @Assert\Length(max="30",maxMessage="Max : 30 caractères")
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="Veuillez saisir votre prénom")
     * @Assert\Length(max="30",maxMessage="Max : 30 caractères")
     * @ORM\Column(type="string", length=30)
     */
    private $prenom;

    /**
     * @Assert\Length(min = 10, max = 11, minMessage = "Veuillez saisir un numéro de téléphone valide",
     *     maxMessage = "Veuillez saisir un numéro de téléphone valide")
     * @Assert\Regex(pattern="/^[0-9]*$/", message="Veuillez saisir un numéro de téléphone valide")
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un nom d'utilisateur")
     * @Assert\Length(min="4", max="30",
     *     minMessage="Le pseudo doit contenir au moins 4 caractères et au maximum 30 caractères",
     *     maxMessage="Le pseudo doit contenir au moins 4 caractères et au maximum 30 caractères")
     * @ORM\Column(type="string", length=30, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=20)
     * * @Assert\Email(
     *     message = "l'email '{{ value }}' n'est par valide",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Veuillez saisir un mot de passe")
     * Assert\Length(min="4", max="16",
     *     minMessage="Le mot de passe doit contenir au moins 4 caractères et au maximum 16 caractères",
     *     maxMessage="Le mot de passe doit contenir au moins 4 caractères et au maximum 16 caractères")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inscription", mappedBy="user")
     */
    private $inscriptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="organisateur")
     */
    private $outings;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", inversedBy="user", cascade={"persist", "remove"})
     */
    private $picture;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
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

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdministrateur()
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur)
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif()
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

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
            $inscription->setParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription)
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getParticipant() === $this) {
                $inscription->setParticipant(null);
            }
        }

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
            $outing->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing)
    {
        if ($this->outings->contains($outing)) {
            $this->outings->removeElement($outing);
            // set the owning side to null (unless already changed)
            if ($outing->getOrganisateur() === $this) {
                $outing->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPicturePath()
    {
        $pictureName = '';
        $picture = $this->getPicture();
        if($picture!=null){
            $pictureName = $picture->getImg();
        }
        return 'uploads/pictures/'.$pictureName;
    }
}
