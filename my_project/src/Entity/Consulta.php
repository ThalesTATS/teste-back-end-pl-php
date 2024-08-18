<?php

namespace App\Entity;

use App\Repository\ConsultaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsultaRepository::class)]
class Consulta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Beneficiario $beneficiario = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medico $medico = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hospital $hospital = null;

    /**
     * @var Collection<int, Observacao>
     */
    #[ORM\OneToMany(targetEntity: Observacao::class, mappedBy: 'consulta', orphanRemoval: true)]
    private Collection $observacaos;

    public function __construct()
    {
        $this->observacaos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getBeneficiario(): ?Beneficiario
    {
        return $this->beneficiario;
    }

    public function setBeneficiario(?Beneficiario $beneficiario): static
    {
        $this->beneficiario = $beneficiario;

        return $this;
    }

    public function getMedico(): ?Medico
    {
        return $this->medico;
    }

    public function setMedico(?Medico $medico): static
    {
        $this->medico = $medico;

        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): static
    {
        $this->hospital = $hospital;

        return $this;
    }

    /**
     * @return Collection<int, Observacao>
     */
    public function getObservacaos(): Collection
    {
        return $this->observacaos;
    }

    public function addObservacao(Observacao $observacao): static
    {
        if (!$this->observacaos->contains($observacao)) {
            $this->observacaos->add($observacao);
            $observacao->setConsulta($this);
        }

        return $this;
    }

    public function removeObservacao(Observacao $observacao): static
    {
        if ($this->observacaos->removeElement($observacao)) {
            // set the owning side to null (unless already changed)
            if ($observacao->getConsulta() === $this) {
                $observacao->setConsulta(null);
            }
        }

        return $this;
    }
}
