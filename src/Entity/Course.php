<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
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

    #[ORM\Column(length: 500)]
    private ?string $img_path = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Category::class)]
    private Collection $category;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: BuyUserCourse::class)]
    private Collection $buyUserCourses;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->buyUserCourses = new ArrayCollection();
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

    public function getImgPath(): ?string
    {
        return $this->img_path;
    }

    public function setImgPath(string $img_path): self
    {
        $this->img_path = $img_path;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
            $category->setCourse($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCourse() === $this) {
                $category->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BuyUserCourse>
     */
    public function getBuyUserCourses(): Collection
    {
        return $this->buyUserCourses;
    }

    public function addBuyUserCourse(BuyUserCourse $buyUserCourse): self
    {
        if (!$this->buyUserCourses->contains($buyUserCourse)) {
            $this->buyUserCourses->add($buyUserCourse);
            $buyUserCourse->setCourse($this);
        }

        return $this;
    }

    public function removeBuyUserCourse(BuyUserCourse $buyUserCourse): self
    {
        if ($this->buyUserCourses->removeElement($buyUserCourse)) {
            // set the owning side to null (unless already changed)
            if ($buyUserCourse->getCourse() === $this) {
                $buyUserCourse->setCourse(null);
            }
        }

        return $this;
    }

    public function getDataInArray(){
        $array = [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "document_root" => $this->document_root,
            "price" => $this->price,
            "img_path" => $this->img_path,
            "user" => $this->user,
            "category" => $this->category,
            "buy_user_courses" => $this->buyUserCourses
        ];
        return $array;
    }
}
