<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @var Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalFilename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Gallery
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="images")
     * @ORM\JoinColumn(referencedColumnName="id", name="gallery_id", nullable=true)
     */
    private $gallery;

    public function __construct(UuidInterface $id, $originalFilename, $filename)
    {
        $this->id = $id;
        $this->originalFilename = $originalFilename;
        $this->filename = $filename;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function canEdit(User $user)
    {
        return $this->getGallery()->isOwner($user);
    }
}
