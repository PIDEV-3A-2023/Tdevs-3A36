<?php

namespace App\Entity;

use App\Repository\BeneficiaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: BeneficiaireRepository::class)]
#[UniqueEntity(fields: ['email_benef'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['email_benef'], message: 'There is already an account with this email')]
class Beneficiaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_benef = null;

    #[ORM\Column(length: 13)]
    #[Assert\NotBlank(message:"Rib is required.")]
    private ?string $rib_benef = null;

    #[ORM\Column(length: 13)]
    #[Assert\NotBlank(message:"Family name is required.")]
    private ?string $nom_benef = null;

    #[ORM\Column(length: 13)]
    #[Assert\NotBlank(message:"Name is required.")]
    private ?string $prenom_benef = null;

    
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message:"Email is required.")]
    #[Assert\Email(message:" The Email '{{ value }}' is not a valid email. ")]
    private ?string $email_benef = null;
    

    // Jointure

    #[ORM\OneToMany(mappedBy: 'beneficiaire', targetEntity: Virement::class)]
    #[ORM\JoinColumn( referencedColumnName:"id_virement")]
    private Collection $virements;




    public function __construct()
    {
        $this->virements = new ArrayCollection();
    }

     
   

    public function getIdBenef(): ?int
    {
        return $this->id_benef;
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

    public function getNomBenef(): ?string
    {
        return $this->nom_benef;
    }

    public function setNomBenef(string $nom_benef): self
    {
        $this->nom_benef = $nom_benef;

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

    public function getEmailBenef(): ?string
    {
        return $this->email_benef;
    }

    public function setEmailBenef(string $email_benef): self
    {
        $this->email_benef = $email_benef;

        return $this;
    }

    /**
     * @return Collection<int, Virement>
     */
    public function getVirements(): Collection
    {
        return $this->virements;
    }

    public function addVirement(Virement $virement): self
    {
        if (!$this->virements->contains($virement)) {
            $this->virements->add($virement);
            $virement->setBeneficiaire($this);
        }

        return $this;
    }

    public function removeVirement(Virement $virement): self
    {
        if ($this->virements->removeElement($virement)) {
            // set the owning side to null (unless already changed)
            if ($virement->getBeneficiaire() === $this) {
                $virement->setBeneficiaire(null);
            }
        }

        return $this;
    }
}
