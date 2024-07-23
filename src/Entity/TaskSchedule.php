<?php

namespace App\Entity;

use App\Repository\TaskScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskScheduleRepository::class)]
class TaskSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $script_path = null;

    #[ORM\Column(length: 255)]
    private ?string $time_execution = null;

    #[ORM\Column(length: 255)]
    private ?string $outputlog_path = null;

    #[ORM\Column]
    private ?bool $can_init = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_execution_time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getScriptPath(): ?string
    {
        return $this->script_path;
    }

    public function setScriptPath(string $script_path): static
    {
        $this->script_path = $script_path;

        return $this;
    }

    public function getTimeExecution(): ?string
    {
        return $this->time_execution;
    }

    public function setTimeExecution(string $time_execution): static
    {
        $this->time_execution = $time_execution;

        return $this;
    }

    public function getOutputlogPath(): ?string
    {
        return $this->outputlog_path;
    }

    public function setOutputlogPath(string $outputlog_path): static
    {
        $this->outputlog_path = $outputlog_path;

        return $this;
    }

    public function isCanInit(): ?bool
    {
        return $this->can_init;
    }

    public function setCanInit(bool $can_init): static
    {
        $this->can_init = $can_init;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLastExecutionTime(): ?\DateTimeInterface
    {
        return $this->last_execution_time;
    }

    public function setLastExecutionTime(?\DateTimeInterface $last_execution_time): static
    {
        $this->last_execution_time = $last_execution_time;

        return $this;
    }
}
