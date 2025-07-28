<?php

namespace App\Entity;

use App\Repository\LicitacaoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicitacaoRepository::class)]
#[ORM\Table(name: 'licitacoes')]
#[ORM\HasLifecycleCallbacks]
class Licitacao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $numeroEdital = null;

    #[ORM\Column(length: 255)]
    private ?string $orgaoResponsavel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dataPublicacao = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getNumeroEdital(): ?string
    {
        return $this->numeroEdital;
    }

    public function setNumeroEdital(string $numeroEdital): static
    {
        $this->numeroEdital = $numeroEdital;

        return $this;
    }

    public function getOrgaoResponsavel(): ?string
    {
        return $this->orgaoResponsavel;
    }

    public function setOrgaoResponsavel(string $orgaoResponsavel): static
    {
        $this->orgaoResponsavel = $orgaoResponsavel;

        return $this;
    }

    public function getDataPublicacao(): ?\DateTime
    {
        return $this->dataPublicacao;
    }

    public function setDataPublicacao(\DateTime $dataPublicacao): static
    {
        $this->dataPublicacao = $dataPublicacao;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
    }
}
