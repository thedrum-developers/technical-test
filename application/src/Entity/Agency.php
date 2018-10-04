<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgencyRepository")
 * @UniqueEntity("contactEmail")
 * @UniqueEntity("webAddress")
 */
class Agency extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(name="contact_email", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $contactEmail;

    /**
     * @ORM\Column(name="web_address", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $webAddress;

    /**
     * @ORM\Column(name="short_description", type="text")
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $established;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", inversedBy="agencies")
     */
    private $services;

    /**
     * Agency constructor.
     */
    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Agency
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contact_email
     *
     * @return Agency
     */
    public function setContactEmail(string $contact_email): self
    {
        $this->contactEmail = $contact_email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getWebAddress(): ?string
    {
        return $this->webAddress;
    }

    /**
     * @param string $web_address
     *
     * @return Agency
     */
    public function setWebAddress(string $web_address): self
    {
        $this->webAddress = $web_address;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $short_description
     *
     * @return Agency
     */
    public function setShortDescription(string $short_description): self
    {
        $this->shortDescription = $short_description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEstablished(): ?string
    {
        return $this->established;
    }

    /**
     * @param string $established
     *
     * @return Agency
     */
    public function setEstablished(string $established): self
    {
        $this->established = $established;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * @param Service $service
     *
     * @return Agency
     */
    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    /**
     * @param Service $service
     *
     * @return Agency
     */
    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
        }

        return $this;
    }
}
