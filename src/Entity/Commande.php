<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date_start;

    /**
     * @ORM\OneToOne(targetEntity=Facturation::class, mappedBy="commande", cascade={"persist", "remove"})
     */
    private $facturation;

    /**
     * @ORM\OneToMany(targetEntity=Journee::class, mappedBy="commande")
     */
    private $journees;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="commandes")
     */
    private $location;

    public function __construct()
    {
        $this->journees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getFacturation(): ?Facturation
    {
        return $this->facturation;
    }

    public function setFacturation(Facturation $facturation): self
    {
        // set the owning side of the relation if necessary
        if ($facturation->getCommande() !== $this) {
            $facturation->setCommande($this);
        }

        $this->facturation = $facturation;

        return $this;
    }

    /**
     * @return Collection|Journee[]
     */
    public function getJournees(): Collection
    {
        return $this->journees;
    }

    public function addJournee(Journee $journee): self
    {
        if (!$this->journees->contains($journee)) {
            $this->journees[] = $journee;
            $journee->setCommande($this);
        }

        return $this;
    }

    public function removeJournee(Journee $journee): self
    {
        if ($this->journees->removeElement($journee)) {
            // set the owning side to null (unless already changed)
            if ($journee->getCommande() === $this) {
                $journee->setCommande(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
