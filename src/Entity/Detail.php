<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $userId = null;

    #[ORM\Column(type: 'text')]
    private ?string $data = null;

    #[ORM\ManyToOne(targetEntity: HeaderProcess::class, inversedBy: 'details')]
    private ?HeaderProcess $headerProcess = null;

    // Getters y setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getHeaderProcess(): ?HeaderProcess
    {
        return $this->headerProcess;
    }

    public function setHeaderProcess(?HeaderProcess $headerProcess): static
    {
        $this->headerProcess = $headerProcess;

        return $this;
    }
}
