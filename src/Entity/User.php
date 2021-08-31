<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\Timestamp;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"}, message="There already is an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestamp;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message = "Email cannot be blank")
     * @Assert\Email(message="Please enter a valid email")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "Password cannot be blank")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "First name cannot be blank")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Last name cannot be blank")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $CIN;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $agency;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $division;

    /**
     * @ORM\OneToMany(targetEntity=Answers::class, mappedBy="client", orphanRemoval=true)
     */
    private $answers;

    // /**
    //  * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="client")
    //  */
    // private $answers;

    // /**
    //  * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="client")
    //  */
    // private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCIN(): ?string
    {
        return $this->CIN;
    }

    public function setCIN(?string $CIN): self
    {
        $this->CIN = $CIN;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAgency(): ?string
    {
        return $this->agency;
    }

    public function setAgency(?string $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(?string $division): self
    {
        $this->division = $division;

        return $this;
    }

    // // /**
    // //  * @return Collection|Answer[]
    // //  */
    // // public function getAnswers(): Collection
    // // {
    // //     return $this->answers;
    // // }

    // // public function addAnswer(Answer $answer): self
    // // {
    // //     if (!$this->answers->contains($answer)) {
    // //         $this->answers[] = $answer;
    // //         $answer->setClient($this);
    // //     }

    // //     return $this;
    // // }

    // // public function removeAnswer(Answer $answer): self
    // // {
    // //     if ($this->answers->removeElement($answer)) {
    // //         // set the owning side to null (unless already changed)
    // //         if ($answer->getClient() === $this) {
    // //             $answer->setClient(null);
    // //         }
    // //     }

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
    //         $answer->setClient($this);
    //     }

    //     return $this;
    // }

    // public function removeAnswer(Answer $answer): self
    // {
    //     if ($this->answers->removeElement($answer)) {
    //         // set the owning side to null (unless already changed)
    //         if ($answer->getClient() === $this) {
    //             $answer->setClient(null);
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
            $answer->setClient($this);
        }

        return $this;
    }

    public function removeAnswer(Answers $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getClient() === $this) {
                $answer->setClient(null);
            }
        }

        return $this;
    }
}