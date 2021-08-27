<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=QuestionsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Questions
{
    use Timestamp;
    public const NUM_ITEMS_PER_PAGE = 8;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Description cannot be blank")
     * @Assert\Length(min=10)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valid;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Survey::class, inversedBy="question")
     */
    private $survey;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }
}