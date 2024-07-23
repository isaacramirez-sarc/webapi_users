<?php

namespace App\Entity;

use App\Repository\SummaryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SummaryRepository::class)]
class Summary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $data = null;

    #[ORM\ManyToOne(targetEntity: HeaderProcess::class, inversedBy: 'summaries')]
    private ?HeaderProcess $headerProcess = null;

    // Getters y setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        $dataArray = json_decode($this->data, true);
        $csvLines = [];
    
        foreach ($dataArray as $item) {
            if (isset($item['line'])) {
                $csvLines[] = $item['line'];
            }
        }
    
        return implode("\n", $csvLines);
    }

    public function setData(string $csvContent): static
    {
        $lines = explode("\n", trim($csvContent));
        $jsonArray = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $jsonArray[] = ['line' => ''];
            } else {
                $jsonArray[] = ['line' => $line];
            }
        }
        $this->data = json_encode($jsonArray);

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
