<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $course_name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $course_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourseName(): ?string
    {
        return $this->course_name;
    }

    public function setCourseName(?string $course_name): static
    {
        $this->course_name = $course_name;

        return $this;
    }

    public function getCourseType(): ?string
    {
        return $this->course_type;
    }

    public function setCourseType(?string $course_type): static
    {
        $this->course_type = $course_type;

        return $this;
    }
}
