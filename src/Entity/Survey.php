<?php

namespace App\Entity;

use App\Repository\SurveyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\Timestamp;

/**
 * @ORM\Entity(repositoryClass=SurveyRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Survey
{
    use Timestamp;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Description cannot be blank")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Questions::class, mappedBy="survey")
     */
    private $question;

    public function __construct()
    {
        $this->question = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * @return Collection|Questions[]
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }

    public function addQuestion(Questions $question): self
    {
        if (!$this->question->contains($question)) {
            $this->question[] = $question;
            $question->setSurvey($this);
        }

        return $this;
    }

    public function removeQuestion(Questions $question): self
    {
        if ($this->question->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getSurvey() === $this) {
                $question->setSurvey(null);
            }
        }

        return $this;
    }
}