<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CoursePurchasesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursePurchasesRepository::class)]
#[ApiResource]
class CoursePurchases
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $transaction_date = null;

    #[ORM\ManyToOne(inversedBy: 'CoursePurchases')]
    private ?courses $course_id = null;

    #[ORM\ManyToOne(inversedBy: 'CoursePurchases')]
    private ?users $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transaction_date;
    }

    public function setTransactionDate(\DateTimeInterface $transaction_date): self
    {
        $this->transaction_date = $transaction_date;

        return $this;
    }

    public function getIdCourse(): ?courses
    {
        return $this->course_id;
    }

    public function setIdCourse(?courses $course_id): self
    {
        $this->course_id = $course_id;

        return $this;
    }

    public function getIdUser(): ?users
    {
        return $this->user_id;
    }

    public function setIdUser(?users $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
