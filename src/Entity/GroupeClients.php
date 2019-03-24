<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupeClientsRepository")
 */
class GroupeClients
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    protected $listeInfoClient;
    
    public function __construct()
    { $this->listeInfoClient=new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListeInfoClient()
    {
        return $this->listeInfoClient;
    }

    public function setListeInfoClient(array $listeInfoClient): self
    {
        $this->listeInfoClient = $listeInfoClient;

        return $this;
    }
}
