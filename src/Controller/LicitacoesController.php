<?php

namespace App\Controller;

use App\Dto\LicitacaoDto;
use App\Entity\Licitacao;
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
        private LicitacaoService $LicitacaoService,
        private ValidatorInterface $validator,
        private LicitacaoRepository $licitacaoRepository
    ) {}

    #[Route('/licitacoes', name: 'listar_licitacoes', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LicitacoesController.php',
        ]);
    }

    #[Route('/licitacoes', name: 'criar_licitacao', methods: ['POST'])]
    public function create(
        Request $request,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(!$data || !isset($data['titulo'], $data['numeroEdital'], $data['orgaoResponsavel'], $data['dataPublicacao'])) {
            return $this->json(
                ['error' => 'Preencha todos os campos.'], 400
            );
        };

        $dto = new LicitacaoDto();
        $dto->titulo = $data['titulo'];
        $dto->numeroEdital = $data['numeroEdital'];
        $dto->orgaoResponsavel = $data['orgaoResponsavel'];
        $dto->dataPublicacao = $data['dataPublicacao'];

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
                'message' => 'Já existe uma licitação com está numeração de edital'
            ]);
        }

        try {
            $this->LicitacaoService->criarLicitacao($dto);

            return $this->json([
                'message' => 'Licitação criada com sucesso!',
            ], 201);
        } catch (\InvalidArgumentException $erro) {
            return $this->json([
            'message' => $erro->getMessage(),
        ], 404);
        }


    }

}
