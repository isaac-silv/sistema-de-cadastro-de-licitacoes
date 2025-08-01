<?php

namespace App\Service;

use App\Dto\CreateLicitacaoDto;
use App\Dto\UpdateLicitacaoDto;
use App\Entity\Licitacao;
use App\Repository\LicitacaoRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LicitacaoService {
    public function __construct(
        private LicitacaoRepository $licitacaoRepository,
        private ValidatorInterface $validator
    ) {}

    public function criarLicitacao(CreateLicitacaoDto $licitacaoDto)
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
        $licitacao->setValorEstimado($licitacaoDto->valorEstimado);

        $this->licitacaoRepository->save($licitacao);

        return $licitacao;
    }

    public function atualizaLicitacao(int $id, UpdateLicitacaoDto $dto): Licitacao {

        $licitacao = $this->licitacaoRepository->find($id);

        if(!$licitacao) {
            throw new \RuntimeException('Licitação não encontrada');
        }

        if($dto->titulo !== null) { $licitacao->setTitulo($dto->titulo); }
        if($dto->numeroEdital !== null) { $licitacao->setNumeroEdital($dto->numeroEdital); }
        if($dto->orgaoResponsavel !== null) { $licitacao->setOrgaoResponsavel($dto->orgaoResponsavel); }
        if($dto->dataPublicacao !== null) { $licitacao->setDataPublicacao($dto->dataPublicacao); }
        if($dto->valorEstimado !== null) { $licitacao->setValorEstimado($dto->valorEstimado); }

        $outraLicitacao = $this->licitacaoRepository->findByEdital($dto->numeroEdital);

        if ($outraLicitacao !== null && $outraLicitacao->getId() !== $id) {
            throw new \RuntimeException('Já existe uma licitação com este número de edital');
        }

        $this->licitacaoRepository->save($licitacao);
        return $licitacao;

    }

    public function listarLicitacoes(): array {
        return $this->licitacaoRepository->findAll();
    }

    public function buscarLicitacao(int $id): ?Licitacao {
        $licitacao = $this->licitacaoRepository->find($id);
        return $licitacao;
    }

    public function removerLicitacao(int $id): void {
        $licitacao = $this->licitacaoRepository->find($id);
        if($licitacao) {
            $this->licitacaoRepository->remove($licitacao);
        } else {
            throw new \RuntimeException('Licitação não encontrada');
        }
    }

}