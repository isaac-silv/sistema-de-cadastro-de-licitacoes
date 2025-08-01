<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateLicitacaoDto
{
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $titulo;

    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 100)]
    public ?string $numeroEdital;

    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $orgaoResponsavel;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dataPublicacao = null;

    #[Assert\Type('string')]
    #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
    public ?string $valorEstimado;
}
