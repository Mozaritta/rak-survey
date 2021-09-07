<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $todo;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Form::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $form;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTodo(): ?bool
    {
        return $this->todo;
    }

    public function setTodo(bool $todo): self
    {
        $this->todo = $todo;

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

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): self
    {
        $this->form = $form;

        return $this;
    }
}
