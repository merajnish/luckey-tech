<?php

namespace App\Entity;

use App\Repository\FeesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeesRepository::class)]
class Fees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Student $student = null;

    #[ORM\ManyToOne]
    private ?Course $course = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $recipt_no = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $payment_mode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getReciptNo(): ?string
    {
        return $this->recipt_no;
    }

    public function setReciptNo(?string $recipt_no): static
    {
        $this->recipt_no = $recipt_no;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentMode(): ?string
    {
        return $this->payment_mode;
    }

    public function setPaymentMode(?string $payment_mode): static
    {
        $this->payment_mode = $payment_mode;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
