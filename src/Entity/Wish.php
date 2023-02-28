<?php

namespace App\Entity;

use App\Repository\WishRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 250)]
    #[Assert\NotBlank(message: 'Merci de renseigner un nom de souhait !')]
    #[Assert\Length(
        min : 1,
        max : 255,
        minMessage: 'Please edit minimum {{ limit }} characters !',
        maxMessage: 'Maximum {{ limit }} characters please !'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max : 3000,
        maxMessage: 'Maximum {{ limit }} caractères !'
    )]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Merci de renseigner un auteur !')]
    #[Assert\Length(
        min : 2,
        max : 50,
        minMessage: 'Merci de renseigner au moins {{ limit }} caractère !',
        maxMessage: 'Maximum {{ limit }} caractères !'
    )]
    private ?string $author = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreated = null;

    //Fetch : 'EAGER' permet de forcer le chargement des éléments (via une jointure)
    #[ORM\ManyToOne(inversedBy: 'wishes', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    #[ORM\PrePersist]
    public function setNewWish(): void
    {
        //Sette automatiquement une date de création du wish par l'utilisateur
        $this->setDateCreated(new \DateTime());

        //Sette automatiquement isPublished du wish par l'utilisateur
        $this->setIsPublished(true);
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }


}
