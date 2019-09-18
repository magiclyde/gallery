<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(name="galleries")
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */
class Gallery
{

    /**
     * @var Uuid
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Image", orphanRemoval=true, cascade={"persist", "remove"}, mappedBy="gallery")
     */
    private $images;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="galleries")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->images = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

    /**
     * @return Uuid
     */
    public function getId()
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function addImage(Image $image)
    {
        if (false === $this->images->contains($image)) {
            $image->setGallery($this);
            $this->images->add($image);
        }
    }

    public function removeImage(Image $image)
    {
        if (true === $this->images->contains($image)) {
            $this->images->removeElement($image);
        }
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function isOwner(User $user)
    {
        return $this->user === $user;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
