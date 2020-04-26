<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDachat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Panier", inversedBy="commandes")
     */
    private $Panier;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDachat(): ?\DateTimeInterface
    {
        return $this->dateDachat;
    }

    public function setDateDachat(\DateTimeInterface $dateDachat): self
    {
        $this->dateDachat = $dateDachat;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->Panier;
    }

    public function setPanier(?Panier $Panier): self
    {
        $this->Panier = $Panier;

        return $this;
    }

}
