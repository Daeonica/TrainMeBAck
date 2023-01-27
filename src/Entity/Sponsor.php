<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\Column(length: 9)]
    private ?string $phone = null;

    #[ORM\Column(length: 500)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'sponsor', targetEntity: Contability::class)]
    private Collection $contabilities;

    public function __construct()
    {
        $this->contabilities = new ArrayCollection();
    }

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

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Contability>
     */
    public function getContabilities(): Collection
    {
        return $this->contabilities;
    }

    public function addContability(Contability $contability): self
    {
        if (!$this->contabilities->contains($contability)) {
            $this->contabilities->add($contability);
            $contability->setSponsor($this);
        }

        return $this;
    }

    public function removeContability(Contability $contability): self
    {
        if ($this->contabilities->removeElement($contability)) {
            // set the owning side to null (unless already changed)
            if ($contability->getSponsor() === $this) {
                $contability->setSponsor(null);
            }
        }

        return $this;
    }
}
