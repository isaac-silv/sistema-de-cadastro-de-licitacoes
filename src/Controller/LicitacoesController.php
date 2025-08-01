<?php

namespace App\Controller;

use App\Dto\CreateLicitacaoDto;
use App\Dto\FindLicitacaoDto;
use App\Dto\UpdateLicitacaoDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\LicitacaoRepository;
use App\Service\LicitacaoService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LicitacoesController extends AbstractController {
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

    #[Route('/licitacoes/{id}', name: 'atualizar_licitação', methods: ['PATCH'])]
    public function atualiza(int $id, Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new UpdateLicitacaoDto();
        $dto->titulo = $data['titulo'] ?? null;
        $dto->numeroEdital = $data['numeroEdital'] ?? null;
        $dto->orgaoResponsavel = $data['orgaoResponsavel'] ?? null;
        $dto->dataPublicacao = isset($data['dataPublicacao']) && $data['dataPublicacao']
            ? new \DateTime($data['dataPublicacao'])
            : null;
        $dto->valorEstimado = $data['valorEstimado'] ?? null;

        $errors = $this->validator->validate($dto);
        if(count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json([
                'message' => 'Erro ao atualizar licitação',
                'errors' => $errorMessages
            ], 400);
        }

        try {
            $licitacao = $this->licitacaoService->atualizaLicitacao($id, $dto);
            return $this->json([
                'id' => $licitacao->getId(),
                'titulo' => $licitacao->getTitulo(),
                'numeroEdital' => $licitacao->getNumeroEdital(),
                'orgaoResponsavel' => $licitacao->getOrgaoResponsavel(),
                'dataPublicacao' => $licitacao->getDataPublicacao()->format('Y-m-d'),
                'valorEstimado' => $licitacao->getValorEstimado()
            ]);

        } catch (\RuntimeException $error) {
            return $this->json([
                'message' => 'Error ao autalizar licitação',
                'error' => $error->getMessage()
            ], 400);
        }
    }

    #[Route('/licitacoes', name: 'listar_licitacoes', methods: ['GET'])]
    public function listar(): JsonResponse {
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

    #[Route('/licitacoes/{id}', name: 'buscar_licitacao', methods: ['GET'])]
    public function buscar(int $id): JsonResponse {
        try {
            $data = $this->licitacaoService->buscarLicitacao($id);
            if(!$data) {
                return $this->json([
                    'message' => 'Nenhuma licitação encontrada'
                ], 400);
            }

            $dto = new FindLicitacaoDto([
                'id' => $data->getId(),
                'titulo' => $data->getTitulo(),
                'numeroEdital' => $data->getNumeroEdital(),
                'orgaoResponsavel' => $data->getOrgaoResponsavel(),
                'dataPublicacao' => $data->getDataPublicacao(),
                'valorEstimado' => $data->getValorEstimado()
            ]);


            return $this->json($dto);

        } catch (\Throwable $erro) {
            return $this->json([
                'message' => 'Erro ao buscar licitação',
                'error' => $erro->getMessage()
            ], 400);
        }
    }

    #[Route('/licitacoes/{id}', name: 'remover_licitacao', methods: ['DELETE'])]
    public function remover(int $id): JsonResponse {
        try {
            $this->licitacaoService->removerLicitacao($id);
            return $this->json([
                'message' => 'Licitação removida com sucesso'
            ]);
        } catch (\RuntimeException $error) {
            return $this->json([
                'message' => 'Erro ao remover licitação',
                'error' => $error->getMessage()
            ], 400);
        }
    }

}
