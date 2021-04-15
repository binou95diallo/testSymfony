<?php

namespace App\Entity;

use App\Repository\ParcelsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParcelsRepository::class)
 */
class Parcels
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destinataireName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $expediteurName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="parcels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDestinataireName(): ?string
    {
        return $this->destinataireName;
    }

    public function setDestinataireName(string $destinataireName): self
    {
        $this->destinataireName = $destinataireName;

        return $this;
    }

    public function getExpediteurName(): ?string
    {
        return $this->expediteurName;
    }

    public function setExpediteurName(string $expediteurName): self
    {
        $this->expediteurName = $expediteurName;

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

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }
}
