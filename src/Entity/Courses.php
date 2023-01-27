<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CoursesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursesRepository::class)]
#[ApiResource]
class Courses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500)]
    private ?string $description = null;

    #[ORM\Column(length: 500)]
    private ?string $document_root = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'Courses')]
    private ?users $id_user = null;

    #[ORM\ManyToOne(inversedBy: 'Courses')]
    private ?Categories $Categories = null;

    #[ORM\OneToMany(mappedBy: 'course_id', targetEntity: CoursePurchases::class)]
    private Collection $coursePurchases;

    #[ORM\Column(length: 500)]
    private ?string $img_path = null;

    public function __construct()
    {
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDocumentRoot(): ?string
    {
        return $this->document_root;
    }

    public function setDocumentRoot(string $document_root): self
    {
        $this->document_root = $document_root;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIdUser(): ?users
    {
        return $this->id_user;
    }

    public function setIdUser(?users $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->Categories;
    }

    public function setCategories(?Categories $Categories): self
    {
        $this->Categories = $Categories;

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
            $coursePurchase->setIdCourse($this);
        }

        return $this;
    }

    public function removeCoursePurchase(CoursePurchases $coursePurchase): self
    {
        if ($this->coursePurchases->removeElement($coursePurchase)) {
            // set the owning side to null (unless already changed)
            if ($coursePurchase->getIdCourse() === $this) {
                $coursePurchase->setIdCourse(null);
            }
        }

        return $this;
    }

    public function getImgPath(): ?string
    {
        return $this->img_path;
    }

    public function setImgPath(string $img_path): self
    {
        $this->img_path = $img_path;

        return $this;
    }
}
