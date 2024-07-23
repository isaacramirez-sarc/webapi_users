<?php

namespace App\Entity;

use App\Repository\HeaderProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeaderProcessRepository::class)]
class HeaderProcess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $executionDate = null;

    #[ORM\OneToMany(mappedBy: 'headerProcess', targetEntity: Summary::class)]
    private $summaries;

    #[ORM\OneToMany(mappedBy: 'headerProcess', targetEntity: Detail::class)]
    private $details;

    // Getters y setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExecutionDate(): ?\DateTimeInterface
    {
        return $this->executionDate;
    }

    public function setExecutionDate(\DateTimeInterface $executionDate): static
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    // Métodos adicionales para manejar summaries y details
}
