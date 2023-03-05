<?php

namespace App\Entity;

use App\Repository\DepensesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
#[ORM\Entity(repositoryClass: DepensesRepository::class)]
class Depenses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_depense = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"le titre doit etre remplir ")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Rib doit etre remplir ")]
    private ?string $prenom_destinataire = null;

    #[ORM\Column(length: 255)]
     /**
     * @Assert\Regex(pattern="/^[0-9]+$/",message="Le rib doit contenir uniquement des entiers" )
     */
    private ?string $rib_destinataire = null;

    #[ORM\Column]
  
    private ?int $montant = null;

    #[ORM\Column(length: 7)]
    private ?string $backgroundcolor = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    /**
     * @Assert\GreaterThan("today", message="La date doit être supérieure à aujourd'hui")
     */
    private ?\DateTimeInterface $datedebut = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie_depense = null;

    #[ORM\Column(length: 255)]
    private ?string $type_depense = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(onDelete:"CASCADE")] 
     

    private ?Budget $idbudget = null;

    public function getIddepense(): ?int
    {
        return $this->id_depense;
    }
    public function setIddepense(string $title): self
    {
        $this->id_depense = $id_depense;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrenomDestinataire(): ?string
    {
        return $this->prenom_destinataire;
    }

    public function setPrenomDestinataire(string $prenom_destinataire): self
    {
        $this->prenom_destinataire = $prenom_destinataire;

        return $this;
    }

    public function getRibDestinataire(): ?string
    {
        return $this->rib_destinataire;
    }

    public function setRibDestinataire(string $rib_destinataire): self
    {
        $this->rib_destinataire = $rib_destinataire;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getBackgroundcolor(): ?string
    {
        return $this->backgroundcolor;
    }

    public function setBackgroundcolor(string $backgroundcolor): self
    {
        $this->backgroundcolor = $backgroundcolor;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getCategorieDepense(): ?string
    {
        return $this->categorie_depense;
    }

    public function setCategorieDepense(string $categorie_depense): self
    {
        $this->categorie_depense = $categorie_depense;

        return $this;
    }

    public function getTypeDepense(): ?string
    {
        return $this->type_depense;
    }

    public function setTypeDepense(string $type_depense): self
    {
        $this->type_depense = $type_depense;

        return $this;
    }

    public function getIdbudget(): ?Budget
    {
        return $this->idbudget;
    }

    public function setIdbudget(?Budget $idbudget): self
    {
        $this->idbudget = $idbudget;

        return $this;
    }
}
