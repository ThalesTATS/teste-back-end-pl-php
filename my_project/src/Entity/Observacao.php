<?php

namespace App\Entity;

use App\Repository\ObservacaoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObservacaoRepository::class)]
class Observacao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descricao = null;

    #[ORM\ManyToOne(inversedBy: 'observacaos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consulta $consulta = null;

    /**
     * @var Collection<int, Anexo>
     */
    #[ORM\OneToMany(targetEntity: Anexo::class, mappedBy: 'observacao', orphanRemoval: true)]
    private Collection $anexos;

    public function __construct()
    {
        $this->anexos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getConsulta(): ?Consulta
    {
        return $this->consulta;
    }

    public function setConsulta(?Consulta $consulta): static
    {
        $this->consulta = $consulta;

        return $this;
    }

    /**
     * @return Collection<int, Anexo>
     */
    public function getAnexos(): Collection
    {
        return $this->anexos;
    }

    public function addAnexo(Anexo $anexo): static
    {
        if (!$this->anexos->contains($anexo)) {
            $this->anexos->add($anexo);
            $anexo->setObservacao($this);
        }

        return $this;
    }

    public function removeAnexo(Anexo $anexo): static
    {
        if ($this->anexos->removeElement($anexo)) {
            // set the owning side to null (unless already changed)
            if ($anexo->getObservacao() === $this) {
                $anexo->setObservacao(null);
            }
        }

        return $this;
    }
}
