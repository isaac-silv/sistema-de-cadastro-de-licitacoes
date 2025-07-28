<?php
namespace App\Dto;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LicitacaoDto
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
}