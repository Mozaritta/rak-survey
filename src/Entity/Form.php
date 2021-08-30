<?php

namespace App\Entity;

use App\Repository\FormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestamp;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FormRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Form
{
    use Timestamp;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Survey::class, mappedBy="form")
     */
    private $survey;

    /**
     * @ORM\Column(type="string", length=100)
     *  @Assert\NotBlank(message = "Name cannot be blank")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->survey = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Survey[]
     */
    public function getSurvey(): Collection
    {
        return $this->survey;
    }

    public function addSurvey(Survey $survey): self
    {
        if (!$this->survey->contains($survey)) {
            $this->survey[] = $survey;
            $survey->setForm($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): self
    {
        if ($this->survey->removeElement($survey)) {
            // set the owning side to null (unless already changed)
            if ($survey->getForm() === $this) {
                $survey->setForm(null);
            }
        }

        return $this;
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
}