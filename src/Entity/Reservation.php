<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $dateBillet;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $typeJour;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreTarifNormal=0;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreTarifReduit=0;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreTarifEnfant=0;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombreTarifSenior=0;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTotal=0;

    /**
     * @ORM\Column(type="integer")
     * 
     */
    private $nombreTotalTicket=0;

    /**
     * @ORM\Column(type="string", length=50)
     * 
     */
    private $btnAjoutPanier=0;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     */
    private $code=0;

    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceTotal(): ?float
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(float $priceTotal): self
    {
        $this->priceTotal = $priceTotal;
        return $this;
    }

    public function getNombreTotalTicket(): ?int
    {
        return $this->nombreTotalTicket;
    }

    public function setNombreTotalTicket(int $nombreTotalTicket): self
    {
        $this->nombreTotalTicket = $nombreTotalTicket;
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

    
     /**
     * Get dateBillet
     *
     * @return \DateTime 
     */
    public function getDateBillet()
    {
        return $this->dateBillet;
    }

    /**
     * Set dateBillet
     *
     * @param \DateTime $dateBillet
     * @return dateBillet
     */
    public function setDateBillet($dateBillet) 
    {
        $this->dateBillet = $dateBillet;

        return $this;
    }

    public function getTypeJour(): ?string
    {
        return $this->typeJour;
    }

    public function setTypeJour(string $typeJour): self
    {
        $this->typeJour = $typeJour;

        return $this;
    }

    public function getNombreTarifNormal(): ?int
    {
        return $this->nombreTarifNormal;
    }

    public function setNombreTarifNormal(int $nombreTarifNormal): self
    {
        $this->nombreTarifNormal = $nombreTarifNormal;

        return $this;
    }

    public function getNombreTarifReduit(): ?int
    {
        return $this->nombreTarifReduit;
    }

    public function setNombreTarifReduit(int $nombreTarifReduit): self
    {
        $this->nombreTarifReduit = $nombreTarifReduit;

        return $this;
    }
    
    public function getNombreTarifEnfant(): ?int
    {
        return $this->nombreTarifEnfant;
    }

    public function setNombreTarifEnfant(int $nombreTarifEnfant): self
    {
        $this->nombreTarifEnfant = $nombreTarifEnfant;

        return $this;
    }

    public function getNombreTarifSenior(): ?int
    {
        return $this->nombreTarifSenior;
    }

    public function setNombreTarifSenior(int $nombreTarifSenior): self
    {
        $this->nombreTarifSenior = $nombreTarifSenior;

        return $this;
    }

    public function getBtnAjoutPanier(): ?string
    {
        return $this->btnAjoutPanier;
    }

    public function setBtnAjoutPanier(string $btnAjoutPanier): self
    {
        $this->btnAjoutPanier = $btnAjoutPanier;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
