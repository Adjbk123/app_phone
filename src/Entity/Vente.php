<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $client = null;

    #[ORM\Column(length: 12)]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?float $montantTotal = null;

    #[ORM\OneToMany(mappedBy: 'vente', targetEntity: DemandeVente::class)]
    private Collection $demandeVentes;

    public function __construct()
    {
        $this->demandeVentes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): self
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    /**
     * @return Collection<int, DemandeVente>
     */
    public function getDemandeVentes(): Collection
    {
        return $this->demandeVentes;
    }

    public function addDemandeVente(DemandeVente $demandeVente): self
    {
        if (!$this->demandeVentes->contains($demandeVente)) {
            $this->demandeVentes->add($demandeVente);
            $demandeVente->setVente($this);
        }

        return $this;
    }

    public function removeDemandeVente(DemandeVente $demandeVente): self
    {
        if ($this->demandeVentes->removeElement($demandeVente)) {
            // set the owning side to null (unless already changed)
            if ($demandeVente->getVente() === $this) {
                $demandeVente->setVente(null);
            }
        }

        return $this;
    }
}
