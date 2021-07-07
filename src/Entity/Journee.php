<?php

namespace App\Entity;

use App\Repository\JourneeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JourneeRepository::class)
 */
class Journee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $adultes;

    /**
     * @ORM\Column(type="smallint")
     */
    private $enfants;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $piscine_enfant;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $piscine_adulte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $majoration;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="journees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdultes(): ?int
    {
        return $this->adultes;
    }

    public function setAdultes(int $adultes): self
    {
        $this->adultes = $adultes;

        return $this;
    }

    public function getEnfants(): ?int
    {
        return $this->enfants;
    }

    public function setEnfants(int $enfants): self
    {
        $this->enfants = $enfants;

        return $this;
    }

    public function getPiscineEnfant(): ?int
    {
        return $this->piscine_enfant;
    }

    public function setPiscineEnfant(?int $piscine_enfant): self
    {
        $this->piscine_enfant = $piscine_enfant;

        return $this;
    }

    public function getPiscineAdulte(): ?int
    {
        return $this->piscine_adulte;
    }

    public function setPiscineAdulte(?int $piscine_adulte): self
    {
        $this->piscine_adulte = $piscine_adulte;

        return $this;
    }

    public function getMajoration(): ?bool
    {
        return $this->majoration;
    }

    public function setMajoration(bool $majoration): self
    {
        $this->majoration = $majoration;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}
