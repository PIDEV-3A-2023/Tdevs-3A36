<?php

namespace App\Entity;

use App\Repository\PretRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[ORM\Index(name: 'pret', columns: ['raison','poste'], flags: ['fulltext'])]

#[ORM\Entity(repositoryClass: PretRepository::class)]
class Pret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    /**
     * @Assert\GreaterThan(
     *     value=1000,
     *     message="Le montant doit être supérieur à 1000 DT"
     * )
     */
    #[ORM\Column]
    private ?float $montant = null;





    #[ORM\Column(length: 255)]
    private ?string $raison = null;


  

    /**
     * @Assert\Length(
     *      min = 3,
     *      
     *      minMessage = "La poste doit comporter au moins 3 caractères",
     *
     * )
     */
    #[ORM\Column]
    private ?string $poste = null;




    /**
     * @Assert\GreaterThan("today", message="La date doit être supérieure à aujourd'hui")
     */

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $debut_travail = null;



    /**
     * @Assert\GreaterThan(
     *     value=0,
     *     message="Le revenu annuel doit être supérieur à zéro"
     * )
     */

    #[ORM\Column]
    private ?float $revenu = null;




    /**
     * @ORM\Column(type="int")
     * @Assert\Range(
     *      min = 12,
     *      max = 84,
     *      notInRangeMessage = "Renseignez une durée comprise entre {{ min }} et {{ max }} mois.",
     * )
     * 
     */

    #[ORM\Column]
    private ?int $duree = null;







    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      max = 100,
     *      notInRangeMessage = "La valeur doit être comprise entre {{ min }} et {{ max }}.",
     * )
     * 
     */

    #[ORM\Column]
    private ?float $Taux = null;

    #[ORM\OneToOne(mappedBy: 'pret', cascade: ['persist', 'remove'])]
    private ?Etatpret $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_pret = null;

    #[ORM\ManyToOne(inversedBy: 'prets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): self
    {
        $this->raison = $raison;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getDebutTravail(): ?\DateTimeInterface
    {
        return $this->debut_travail;
    }

    public function setDebutTravail(\DateTimeInterface $debut_travail): self
    {
        $this->debut_travail = $debut_travail;

        return $this;
    }

    public function getRevenu(): ?float
    {
        return $this->revenu;
    }

    public function setRevenu(float $revenu): self
    {
        $this->revenu = $revenu;

        return $this;
    }



    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->Taux;
    }

    public function setTaux(float $Taux): self
    {
        $this->Taux = $Taux;

        return $this;
    }

    /*   public function getAdministrator(): ?EtatPret
    {
        return $this->administrator;
    }

    public function setAdministrator(?EtatPret $administrator): self
    {
        // unset the owning side of the relation if necessary
        if ($administrator === null && $this->administrator !== null) {
            $this->administrator->setEtat(null);
        }

        // set the owning side of the relation if necessary
        if ($administrator !== null && $administrator->getEtat() !== $this) {
            $administrator->setEtat($this);
        }

        $this->administrator = $administrator;

        return $this;
    } */

    public function getEtat(): ?Etatpret
    {
        return $this->etat;
    }

    public function setEtat(?Etatpret $etat): self
    {
        // unset the owning side of the relation if necessary
        if ($etat === null && $this->etat !== null) {
            $this->etat->setPret(null);
        }

        // set the owning side of the relation if necessary
        if ($etat !== null && $etat->getPret() !== $this) {
            $etat->setPret($this);
        }

        $this->etat = $etat;

        return $this;
    }

    public function getDatePret(): ?\DateTimeInterface
    {
        return $this->date_pret;
    }

    public function setDatePret(?\DateTimeInterface $date_pret): self
    {
        $this->date_pret = $date_pret;

        return $this;
    }

    public function getAccount(): ?account
    {
        return $this->account;
    }

    public function setAccount(?account $account): self
    {
        $this->account = $account;

        return $this;
    }
   
     /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
