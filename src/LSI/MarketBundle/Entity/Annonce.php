<?php

namespace LSI\MarketBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Annonce
 *
 * @ORM\Table(name="annonce")
 * @ORM\Entity(repositoryClass="LSI\MarketBundle\Repository\AnnonceRepository")
 */
class Annonce
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     *     minMessage = "Le titre doit comporter au moins {{ limit }} caractères",
     *     maxMessage = "Le titre ne doit pas comporter plus de {{ limit }} caractères",
     * )
     */
    private $titre;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 5,
     *     max = 100,
     *     minMessage = "La description doit comporter au moins {{ limit }} caractères",
     *     maxMessage = "La description ne doit pas comporter plus de {{ limit }} caractères"
     * )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="regle_cond", type="string", length=255, unique=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 5,
     *     max = 100,
     *     minMessage = "Les règles doivent comporter au moins {{ limit }} caractères",
     *     maxMessage = "Les règles ne doivent pas comporter plus de {{ limit }} caractères",
     * )
     */
    private $regleCond;

    /**
     * @var float
     *
     * @ORM\Column(name="prix_defaut", type="float", precision=10, unique=false)
     * @Assert\NotBlank()
     */
    private $prixDefaut;

    /**
     * @var date
     *
     * @ORM\Column(name="date_creation", type="date")
     */
    private $dateCreation;

    /**
     * @var time
     *
     * @ORM\Column(name="heure_creation", type="time")
     */
    private $heureCreation;

    /**
     * @ORM\Column(name="annonce_update_at", type="datetime", nullable=true)
     * @Assert\IdenticalTo("today")
     */
    private $annonceUpdateAt;

    /**
     * @ORM\Column(name="etat", type="string", length=2)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 1,
     *     max = 2,
     * )
     */
    private $annonceEtat;

    /**
     * @ORM\Column(name="type_annul", type="string", length=10, nullable= true)
     */
    private $typeAnnul;

    /**
     * @ORM\ManyToOne(targetEntity="LSI\MarketBundle\Entity\Mairie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mairie;

    /**
     * @ORM\OneToMany(targetEntity="LSI\MarketBundle\Entity\Image", mappedBy="annonce", cascade={"persist"})
     *@ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=false)
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="LSI\MarketBundle\Entity\Caracteristique", mappedBy="annonce")
     */
    private $caracteristiques;

    /**
     * @ORM\ManyToOne(targetEntity="LSI\MarketBundle\Entity\Categorie", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity="LSI\MarketBundle\Entity\Calendrier", mappedBy="annonce")
     */
    private $calendrier;

    /**
     * @ORM\ManyToOne(targetEntity="LSI\MarketBundle\Entity\Adresse", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $adresse;

    /**
     * @ORM\Column(name="PublicMairie", type="string" , nullable= true)
     */
    private $pulicMairie;

    /**
     * @ORM\Column(name="PublicAdministre", type="string" , nullable= true)
     */
    private $pulicAdministre;

    /**
     * @ORM\OneToOne(targetEntity="LSI\MarketBundle\Entity\TarifAdministreEpci", cascade={"persist"})
     */
    private $tarifAdminisEpci;

    /**
     * @ORM\OneToOne(targetEntity="LSI\MarketBundle\Entity\TarifAdministreNonEpci", cascade={"persist"})
     */
    private $tarifNonAdminisEpci;

    /**
     * @ORM\OneToOne(targetEntity="LSI\MarketBundle\Entity\TarifNonAdministre", cascade={"persist"})
     */
    private $tarifNonAdmins;

    /**
     * @ORM\OneToOne(targetEntity="LSI\MarketBundle\Entity\TarifEpci", cascade={"persist"})
     */
    private $tarifEpci;

    /**
     * @ORM\OneToOne(targetEntity="LSI\MarketBundle\Entity\TarifEpci", cascade={"persist"})
     */
    private $tarifNonEpci;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Constructor
     */
    public function __construct()
    {

        $this->dateCreation = new \DateTime();
        $this->heureCreation = new \DateTime();
        $this->annonceEtat = 'A';
        //$this->images = ArrayCollection::class;
    }


    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Annonce
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Annonce
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set regleCond
     *
     * @param string $regleCond
     *
     * @return Annonce
     */
    public function setRegleCond($regleCond)
    {
        $this->regleCond = $regleCond;

        return $this;
    }

    /**
     * Get regleCond
     *
     * @return string
     */
    public function getRegleCond()
    {
        return $this->regleCond;
    }

    /**
     * Set prixDefaut
     *
     * @param float $prixDefaut
     *
     * @return Annonce
     */
    public function setPrixDefaut($prixDefaut)
    {
        $this->prixDefaut = $prixDefaut;

        return $this;
    }

    /**
     * Get prixDefaut
     *
     * @return float
     */
    public function getPrixDefaut()
    {
        return $this->prixDefaut;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Annonce
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set heureCreation
     *
     * @param \DateTime $heureCreation
     *
     * @return Annonce
     */
    public function setHeureCreation($heureCreation)
    {
        $this->heureCreation = $heureCreation;

        return $this;
    }

    /**
     * Get heureCreation
     *
     * @return \DateTime
     */
    public function getHeureCreation()
    {
        return $this->heureCreation;
    }

    /**
     * Set annonceUpdateAt
     *
     * @param \DateTime $annonceUpdateAt
     *
     * @return Annonce
     */
    public function setAnnonceUpdateAt($annonceUpdateAt)
    {
        $this->annonceUpdateAt = $annonceUpdateAt;

        return $this;
    }

    /**
     * Get annonceUpdateAt
     *
     * @return \DateTime
     */
    public function getAnnonceUpdateAt()
    {
        return $this->annonceUpdateAt;
    }

    /**
     * Set annonceEtat
     *
     * @param string $annonceEtat
     *
     * @return Annonce
     */
    public function setAnnonceEtat($annonceEtat)
    {
        $this->annonceEtat = $annonceEtat;

        return $this;
    }

    /**
     * Get annonceEtat
     *
     * @return string
     */
    public function getAnnonceEtat()
    {
        return $this->annonceEtat;
    }

    /**
     * Set typeAnnul
     *
     * @param string $typeAnnul
     *
     * @return Annonce
     */
    public function setTypeAnnul($typeAnnul)
    {
        $this->typeAnnul = $typeAnnul;

        return $this;
    }

    /**
     * Get typeAnnul
     *
     * @return string
     */
    public function getTypeAnnul()
    {
        return $this->typeAnnul;
    }

    /**
     * Set mairie
     *
     * @param \LSI\MarketBundle\Entity\Mairie $mairie
     *
     * @return Annonce
     */
    public function setMairie(\LSI\MarketBundle\Entity\Mairie $mairie)
    {
        $this->mairie = $mairie;

        return $this;
    }

    /**
     * Get mairie
     *
     * @return \LSI\MarketBundle\Entity\Mairie
     */
    public function getMairie()
    {
        return $this->mairie;
    }

    /**
     * Add image
     *
     * @param \LSI\MarketBundle\Entity\Image $image
     *
     * @return Annonce
     */
    public function addImage(\LSI\MarketBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \LSI\MarketBundle\Entity\Image $image
     */
    public function removeImage(\LSI\MarketBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add caracteristique
     *
     * @param \LSI\MarketBundle\Entity\Caracteristique $caracteristique
     *
     * @return Annonce
     */
    public function addCaracteristique(\LSI\MarketBundle\Entity\Caracteristique $caracteristique)
    {
        $this->caracteristiques[] = $caracteristique;

        return $this;
    }

    /**
     * Remove caracteristique
     *
     * @param \LSI\MarketBundle\Entity\Caracteristique $caracteristique
     */
    public function removeCaracteristique(\LSI\MarketBundle\Entity\Caracteristique $caracteristique)
    {
        $this->caracteristiques->removeElement($caracteristique);
    }

    /**
     * Get caracteristiques
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCaracteristiques()
    {
        return $this->caracteristiques;
    }

    /**
     * Set categorie
     *
     * @param \LSI\MarketBundle\Entity\Categorie $categorie
     *
     * @return Annonce
     */
    public function setCategorie(\LSI\MarketBundle\Entity\Categorie $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \LSI\MarketBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Add calendrier
     *
     * @param \LSI\MarketBundle\Entity\Calendrier $calendrier
     *
     * @return Annonce
     */
    public function addCalendrier(\LSI\MarketBundle\Entity\Calendrier $calendrier)
    {
        $this->calendrier[] = $calendrier;

        return $this;
    }

    /**
     * Remove calendrier
     *
     * @param \LSI\MarketBundle\Entity\Calendrier $calendrier
     */
    public function removeCalendrier(\LSI\MarketBundle\Entity\Calendrier $calendrier)
    {
        $this->calendrier->removeElement($calendrier);
    }

    /**
     * Get calendrier
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalendrier()
    {
        return $this->calendrier;
    }

    /**
     * Set adresse
     *
     * @param \LSI\MarketBundle\Entity\Adresse $adresse
     *
     * @return Annonce
     */
    public function setAdresse(\LSI\MarketBundle\Entity\Adresse $adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return \LSI\MarketBundle\Entity\Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set prix
     *
     * @param \LSI\MarketBundle\Entity\Prix $prix
     *
     * @return Annonce
     */
    public function setPrix(\LSI\MarketBundle\Entity\Prix $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return \LSI\MarketBundle\Entity\Prix
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set pulicMairie
     *
     * @param string $pulicMairie
     *
     * @return Annonce
     */
    public function setPulicMairie($pulicMairie)
    {
        $this->pulicMairie = $pulicMairie;

        return $this;
    }

    /**
     * Get pulicMairie
     *
     * @return string
     */
    public function getPulicMairie()
    {
        return $this->pulicMairie;
    }

    /**
     * Set pulicAdministre
     *
     * @param string $pulicAdministre
     *
     * @return Annonce
     */
    public function setPulicAdministre($pulicAdministre)
    {
        $this->pulicAdministre = $pulicAdministre;

        return $this;
    }

    /**
     * Get pulicAdministre
     *
     * @return string
     */
    public function getPulicAdministre()
    {
        return $this->pulicAdministre;
    }

    /**
     * Set tarifAdminisEpci
     *
     * @param \LSI\MarketBundle\Entity\TarifAdministreEpci $tarifAdminisEpci
     *
     * @return Annonce
     */
    public function setTarifAdminisEpci(\LSI\MarketBundle\Entity\TarifAdministreEpci $tarifAdminisEpci = null)
    {
        $this->tarifAdminisEpci = $tarifAdminisEpci;

        return $this;
    }

    /**
     * Get tarifAdminisEpci
     *
     * @return \LSI\MarketBundle\Entity\TarifAdministreEpci
     */
    public function getTarifAdminisEpci()
    {
        return $this->tarifAdminisEpci;
    }

    /**
     * Set tarifNonAdminisEpci
     *
     * @param \LSI\MarketBundle\Entity\TarifAdministreNonEpci $tarifNonAdminisEpci
     *
     * @return Annonce
     */
    public function setTarifNonAdminisEpci(\LSI\MarketBundle\Entity\TarifAdministreNonEpci $tarifNonAdminisEpci = null)
    {
        $this->tarifNonAdminisEpci = $tarifNonAdminisEpci;

        return $this;
    }

    /**
     * Get tarifNonAdminisEpci
     *
     * @return \LSI\MarketBundle\Entity\TarifAdministreNonEpci
     */
    public function getTarifNonAdminisEpci()
    {
        return $this->tarifNonAdminisEpci;
    }

    /**
     * Set tarifNonAdmins
     *
     * @param \LSI\MarketBundle\Entity\TarifNonAdministre $tarifNonAdmins
     *
     * @return Annonce
     */
    public function setTarifNonAdmins(\LSI\MarketBundle\Entity\TarifNonAdministre $tarifNonAdmins = null)
    {
        $this->tarifNonAdmins = $tarifNonAdmins;

        return $this;
    }

    /**
     * Get tarifNonAdmins
     *
     * @return \LSI\MarketBundle\Entity\TarifNonAdministre
     */
    public function getTarifNonAdmins()
    {
        return $this->tarifNonAdmins;
    }

    /**
     * Set tarifEpci
     *
     * @param \LSI\MarketBundle\Entity\TarifEpci $tarifEpci
     *
     * @return Annonce
     */
    public function setTarifEpci(\LSI\MarketBundle\Entity\TarifEpci $tarifEpci = null)
    {
        $this->tarifEpci = $tarifEpci;

        return $this;
    }

    /**
     * Get tarifEpci
     *
     * @return \LSI\MarketBundle\Entity\TarifEpci
     */
    public function getTarifEpci()
    {
        return $this->tarifEpci;
    }

    /**
     * Set tarifNonEpci
     *
     * @param \LSI\MarketBundle\Entity\TarifEpci $tarifNonEpci
     *
     * @return Annonce
     */
    public function setTarifNonEpci(\LSI\MarketBundle\Entity\TarifEpci $tarifNonEpci = null)
    {
        $this->tarifNonEpci = $tarifNonEpci;

        return $this;
    }

    /**
     * Get tarifNonEpci
     *
     * @return \LSI\MarketBundle\Entity\TarifEpci
     */
    public function getTarifNonEpci()
    {
        return $this->tarifNonEpci;
    }
}
