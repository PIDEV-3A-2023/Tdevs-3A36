<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
use symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups ("messages")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    
    /**
     * @Assert\GreaterThan("today", message="La date doit être supérieure à aujourd'hui")
     */
    private ?\DateTimeInterface $date = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Rendezvous::class, inversedBy: 'messages')]
    private Collection $rdv;

    public function __construct()
    {
        $this->rdv = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

 
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Rendezvous>
     */
    public function getRdv(): Collection
    {
        return $this->rdv;
    }

    public function addRdv(Rendezvous $rdv): self
    {
        if (!$this->rdv->contains($rdv)) {
            $this->rdv->add($rdv);
        }

        return $this;
    }

    public function removeRdv(Rendezvous $rdv): self
    {
        $this->rdv->removeElement($rdv);

        return $this;
    }
}
