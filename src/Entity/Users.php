<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ApiResource]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 500)]
    private ?string $password = null;

    #[ORM\Column(length: 500)]
    private ?string $email = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $register_date = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $img_path = null;

    #[ORM\ManyToOne(inversedBy: 'Users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Roles $role = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Courses::class)]
    private Collection $courses;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Publications::class)]
    private Collection $publications;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: CoursePurchases::class)]
    private Collection $coursePurchases;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->coursePurchases = new ArrayCollection();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->register_date;
    }

    public function setRegisterDate(?\DateTimeInterface $register_date): self
    {
        $this->register_date = $register_date;

        return $this;
    }

    public function getRoleId(): ?Roles
    {
        return $this->role;
    }

    public function setRoleId(?Roles $role_id): self
    {
        $this->role= $role_id;

        return $this;
    }

    /**
     * @return Collection<int, Courses>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Courses $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setIdUser($this);
        }

        return $this;
    }

    public function removeCourse(Courses $course): self
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getIdUser() === $this) {
                $course->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publications>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publications $publication): self
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setIdUser($this);
        }

        return $this;
    }

    public function removePublication(Publications $publication): self
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getIdUser() === $this) {
                $publication->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CoursePurchases>
     */
    public function getCoursePurchases(): Collection
    {
        return $this->coursePurchases;
    }

    public function addCoursePurchase(CoursePurchases $coursePurchase): self
    {
        if (!$this->coursePurchases->contains($coursePurchase)) {
            $this->coursePurchases->add($coursePurchase);
            $coursePurchase->setIdUser($this);
        }

        return $this;
    }

    public function removeCoursePurchase(CoursePurchases $coursePurchase): self
    {
        if ($this->coursePurchases->removeElement($coursePurchase)) {
            // set the owning side to null (unless already changed)
            if ($coursePurchase->getIdUser() === $this) {
                $coursePurchase->setIdUser(null);
            }
        }

        return $this;
    }

    public function getImgPath(): ?string
    {
        return $this->img_path;
    }

    public function setImgPath(?string $img_path): self
    {
        $this->img_path = $img_path;

        return $this;
    }
}
