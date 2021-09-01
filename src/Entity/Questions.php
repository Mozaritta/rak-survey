<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Answers::class, mappedBy="question")
     */
    private $answers;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    // /**
    //  * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question")
    //  */
    // private $answers;

    // public function __construct()
    // {
    //     $this->answers = new ArrayCollection();
    // }

    // /**
    //  * @ORM\OneToOne(targetEntity=Answer::class, mappedBy="question", cascade={"persist", "remove"})
    //  */
    // private $answer;

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

    // // public function getAnswer(): ?Answer
    // // {
    // //     return $this->answer;
    // // }

    // // public function setAnswer(Answer $answer): self
    // // {
    // //     // set the owning side of the relation if necessary
    // //     if ($answer->getQuestion() !== $this) {
    // //         $answer->setQuestion($this);
    // //     }

    // //     $this->answer = $answer;

    // //     return $this;
    // // }

    // /**
    //  * @return Collection|Answer[]
    //  */
    // public function getAnswers(): Collection
    // {
    //     return $this->answers;
    // }

    // public function addAnswer(Answer $answer): self
    // {
    //     if (!$this->answers->contains($answer)) {
    //         $this->answers[] = $answer;
    //         $answer->setQuestion($this);
    //     }

    //     return $this;
    // }

    // public function removeAnswer(Answer $answer): self
    // {
    //     if ($this->answers->removeElement($answer)) {
    //         // set the owning side to null (unless already changed)
    //         if ($answer->getQuestion() === $this) {
    //             $answer->setQuestion(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection|Answers[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answers $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answers $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

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
}