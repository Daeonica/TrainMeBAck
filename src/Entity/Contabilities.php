<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ContabilitiesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContabilitiesRepository::class)]
#[ApiResource]
class Contabilities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $bill_date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $quantity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $concept = null;

    #[ORM\ManyToOne(inversedBy: 'Contabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?sponsors $sponsor_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBillDate(): ?\DateTimeInterface
    {
        return $this->bill_date;
    }

    public function setBillDate(\DateTimeInterface $bill_date): self
    {
        $this->bill_date = $bill_date;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getConcept(): ?string
    {
        return $this->concept;
    }

    public function setConcept(?string $concept): self
    {
        $this->concept = $concept;

        return $this;
    }

    public function getSponsorId(): ?sponsors
    {
        return $this->sponsor_id;
    }

    public function setSponsorId(?sponsors $sponsor_id): self
    {
        $this->sponsor_id = $sponsor_id;

        return $this;
    }
}
