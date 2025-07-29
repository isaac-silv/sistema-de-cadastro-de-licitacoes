<?php

namespace App\Controller;

use App\Dto\CreateLicitacaoDto;
use App\Dto\FindLicitacaoDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LicitacaoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LicitacaoService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LicitacoesController extends AbstractController
{
    public function __construct(
        private LicitacaoService $licitacaoService,
        private ValidatorInterface $validator,
        private LicitacaoRepository $licitacaoRepository
    ) {}

    #[Route('/licitacoes', name: 'criar_licitacao', methods: ['POST'])]
    public function create(
        Request $request,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(!$data || !isset($data['titulo'], $data['numeroEdital'], $data['orgaoResponsavel'], $data['dataPublicacao'])) {
            return $this->json(
                ['error' => 'Preencha todos os campos obrigatorios.'], 400
            );
        };

        $dto = new CreateLicitacaoDto();
        $dto->titulo = $data['titulo'];
        $dto->numeroEdital = $data['numeroEdital'];
        $dto->orgaoResponsavel = $data['orgaoResponsavel'];
        $dto->dataPublicacao = $data['dataPublicacao'];
        $dto->valorEstimado = $data['valorEstimado'];

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errorsMessages = [];

            foreach ($errors as $error) {
                $errorsMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json([
                'message' => 'Erro ao criar licitação.',
                'errors' => $errorsMessages
            ], 400);
        }

        // Verifica licitação existente através do numero do edital
        $licitacaoExistente = $this->licitacaoRepository->findByEdital($dto->numeroEdital);
        if($licitacaoExistente !== null) {
            return $this->json([
                'message' => 'Já existe uma licitação com esta numeração de edital'
            ]);
        }

        try {
            $this->licitacaoService->criarLicitacao($dto);

            return $this->json([
                'message' => 'Licitação criada com sucesso!',
            ], 201);
        } catch (\InvalidArgumentException $erro) {
            return $this->json([
            'message' => $erro->getMessage(),
        ], 404);
        }
    }

    #[Route('/licitacoes', name: 'listar_licitacoes', methods: ['GET'])]
    public function listar(): JsonResponse
    {
        $licitacoes = $this->licitacaoService->listarLicitacoes();
        $response = array_map(function ($licitacao){
            return new FindLicitacaoDto([
                'id' => $licitacao->getId(),
                'titulo' => $licitacao->getTitulo(),
                'numeroEdital' => $licitacao->getNumeroEdital(),
                'orgaoResponsavel' => $licitacao->getOrgaoResponsavel(),
                'dataPublicacao' => $licitacao->getDataPublicacao(),
                'valorEstimado' => $licitacao->getValorEstimado()
            ]);
        }, $licitacoes);
        return $this->json($response);
    }

}
