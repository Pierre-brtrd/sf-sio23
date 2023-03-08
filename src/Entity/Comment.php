<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use App\Entity\Traits\EnabledEntityTrait;
use App\Entity\Traits\DateTimeEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    use DateTimeEntityTrait,
        EnabledEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: "Le titre ne peut pas être vide"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: "Le contenu ne peut pas être vide"
    )]
    private ?string $content = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}"
    )]
    private ?int $grade = null;

    #[ORM\Column]
    #[Assert\NotBlank(
        message: "Vous devez accepter les conditions et la politique de conf"
    )]
    private ?bool $gdpr = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(int $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function isGdpr(): ?bool
    {
        return $this->gdpr;
    }

    public function setGdpr(bool $gdpr): self
    {
        $this->gdpr = $gdpr;

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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
