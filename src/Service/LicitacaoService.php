<?php

namespace App\Service;

use App\Dto\LicitacaoDto;
use App\Entity\Licitacao;
use App\Repository\LicitacaoRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LicitacaoService
{
    public function __construct(
        private LicitacaoRepository $licitacaoRepository,
        private ValidatorInterface $validator
    ) {}

    public function criarLicitacao(LicitacaoDto $licitacaoDto)
    {
        $errors = $this->validator->validate($licitacaoDto);

        if(count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $licitacao = new Licitacao();
        $licitacao->setTitulo($licitacaoDto->titulo);
        $licitacao->setNumeroEdital($licitacaoDto->numeroEdital);
        $licitacao->setOrgaoResponsavel($licitacaoDto->orgaoResponsavel);
        $licitacao->setDataPublicacao(new \DateTime($licitacaoDto->dataPublicacao));

        $this->licitacaoRepository->save($licitacao);

        return $licitacao;
    }
}