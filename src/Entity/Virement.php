<?php

namespace App\Entity;

use App\Repository\VirementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: VirementRepository::class)]
class Virement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_virement = null;

    #[ORM\Column(type : "float")]
    #[Assert\NotBlank(message:"Montant is required.")]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_virement = null;

    /*  public function today_date()
      {
          $this->date_virement = new \DateTime('now');
      } */

    #[ORM\Column(type: 'string' , length: 255)]
    #[Assert\NotBlank(message:"Title is required.")]
    private ?string $titre = null;

    #[ORM\Column(type: 'string' , length: 255)]
    #[Assert\NotBlank(message:"Name is required.")]
    private ?string $prenom_benef = null;

    #[ORM\Column(type: 'string' , length: 255)]
    #[Assert\NotBlank(message:"Rib is required.")]
    private ?string $rib_benef = null;


    // Jointure

    #[ORM\ManyToOne(inversedBy: 'virements')]
    #[ORM\JoinColumn( referencedColumnName:"id_benef")]
    private ?Beneficiaire $beneficiaire = null;




    public function getIdVirement(): ?int
    {
        return $this->id_virement;
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

    public function getDateVirement(): ?\DateTimeInterface
    {
        return $this->date_virement;
    }

    public function setDateVirement(\DateTimeInterface $date_virement): self
    {
        $this->date_virement = $date_virement;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrenomBenef(): ?string
    {
        return $this->prenom_benef;
    }

    public function setPrenomBenef(string $prenom_benef): self
    {
        $this->prenom_benef = $prenom_benef;

        return $this;
    }

    public function getRibBenef(): ?string
    {
        return $this->rib_benef;
    }

    public function setRibBenef(string $rib_benef): self
    {
        $this->rib_benef = $rib_benef;

        return $this;
    }

    public function getBeneficiaire(): ?Beneficiaire
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?Beneficiaire $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }
}
