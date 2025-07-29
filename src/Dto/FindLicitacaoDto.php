<?php

namespace App\Dto;

class FindLicitacaoDto
{
    public int $id;
    public string $titulo;
    public string $orgaoResponsavel;
    public string $numeroEdital;
    public \DateTimeInterface $dataPublicacao;
    public ?float $valorEstimado;

    public function __construct(array $licitacao)
    {
        $this->id = $licitacao['id'];
        $this->titulo = $licitacao['titulo'];
        $this->orgaoResponsavel = $licitacao['orgaoResponsavel'];
        $this->numeroEdital = $licitacao['numeroEdital'];
        $this->dataPublicacao = $licitacao['dataPublicacao'];
        $this->valorEstimado = isset($licitacao['valorEstimado']) ? $licitacao['valorEstimado'] : null;
    }
}