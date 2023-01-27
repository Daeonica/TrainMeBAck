<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
#[ApiResource]
class Roles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $key_value = null;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: Users::class)]
    private Collection $Users;

    public function __construct()
    {
        $this->Users = new ArrayCollection();
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

    public function getKeyValue(): ?string
    {
        return $this->key_value;
    }

    public function setKeyValue(string $key_value): self
    {
        $this->key_value = $key_value;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->Users;
    }

    public function addUser(Users $user): self
    {
        if (!$this->Users->contains($user)) {
            $this->Users->add($user);
            $user->setRoleId($this);
        }

        return $this;
    }

    public function removeUser(Users $user): self
    {
        if ($this->Users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRoleId() === $this) {
                $user->setRoleId(null);
            }
        }

        return $this;
    }
}
