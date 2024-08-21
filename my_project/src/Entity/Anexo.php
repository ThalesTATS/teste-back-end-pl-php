<?php

namespace App\Entity;

use App\Repository\AnexoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnexoRepository::class)]
class Anexo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 255)]
    private ?string $extensao = null;

    #[ORM\Column(length: 255)]
    private ?string $tamanho = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'anexos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Observacao $observacao = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getExtensao(): ?string
    {
        return $this->extensao;
    }

    public function setExtensao(string $extensao): static
    {
        $this->extensao = $extensao;

        return $this;
    }

    public function getTamanho(): ?string
    {
        return $this->tamanho;
    }

    public function setTamanho(string $tamanho): static
    {
        $this->tamanho = $tamanho;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getObservacao(): ?Observacao
    {
        return $this->observacao;
    }

    public function setObservacao(?Observacao $observacao): static
    {
        $this->observacao = $observacao;

        return $this;
    }
}
