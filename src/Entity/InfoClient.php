<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Service\PriceCalculator\PriceCalculator;


/**
 * @ORM\Entity(repositoryClass="App\Repository\InfoClientRepository")
 */
class InfoClient
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;
    
   /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $accesReduit=0;

    /**
     * @ORM\Column(type="float")
     */
    private $priceClient=0;

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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getMessageEmail(): ?string
    {
        return $this->email;
    }

    public function setMessageEmail(string $email): self
    {
        $this->email=$email;
        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return DateNaissance
     */
    public function setDateNaissance( $dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getAccesReduit(): ?string
    {
        return $this->accesReduit;
    }

    public function setAccesReduit(string $accesReduit): self
    {
        $this->accesReduit = $accesReduit;

        return $this;
    }

    public function getPriceClient($typeJour): ?float
    {
        $priceCalculator = new PriceCalculator();
        
        //$anneeNaissance=$dateNaissance->format('Y');
        //$moisNaissance=$dateNaissance->format('m');
        //$jourNaissance=$dateNaissance->format('d');
        $anneeNaissance=($this->dateNaissance)->format('Y');
        $moisNaissance=($this->dateNaissance)->format('m');
        $jourNaissance=($this->dateNaissance)->format('d');
        $reduit=$this->accesReduit;
        $prixClient=$priceCalculator->getTarifClient($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour);
        return $prixClient;
    }

    /*public function setPriceClient(float $priceClient): self
    {
        $this->priceClient = $priceClient;
        return $this;
    }*/
}
