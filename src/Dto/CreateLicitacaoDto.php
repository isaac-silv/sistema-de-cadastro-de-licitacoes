<?php
namespace App\Dto;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateLicitacaoDto
{
    #[Assert\NotBlank(
        message: 'O titulo da licitação é obrigatório.'
    )]
    public string $titulo = '';

    #[Assert\NotBlank(
        message: 'O número do edital da licitação é obrigatório.'
    )]
    public string $numeroEdital = '';

    #[Assert\NotBlank(
        message: 'Orgão responsável da licitação é obrigatório.'
    )]
    public string $orgaoResponsavel = '';

    #[Assert\NotBlank(
        message: 'A data da licitação é obrigatório.'
    )]
    #[Assert\DateTime(
        format: \DateTimeInterface::ATOM,
        message: 'Formato de data invalida.'
    )]
    public string $dataPublicacao;

    #[Assert\Type(
        type: 'numeric',
        message: 'O valor precisa ser um número decimal válido.'
    )]
    #[Assert\PositiveOrZero(
        message: 'Valor inválido.'
    )]
    public $valorEstimado;
}