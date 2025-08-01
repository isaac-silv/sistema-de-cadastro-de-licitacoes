<?php
namespace App\Tests\Controller;

use App\Controller\LicitacoesController;
use App\Entity\Licitacao;
use App\Repository\LicitacaoRepository;
use App\Service\LicitacaoService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LicitacoesControllerTest extends WebTestCase {
    private $client;
    private $entityManager;

    protected function setUp(): void {
        $this->client = static::createClient();

        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $this->entityManager->getConnection()->executeStatement('TRUNCATE TABLE licitacoes RESTART IDENTITY CASCADE');
    }

    public function testCreateLicitacao() {
        $uniqueId = uniqid();
        $data = [
            'titulo' => "Licitação Windows {$uniqueId}",
            'numeroEdital' => "WIN-{$uniqueId}/001",
            'orgaoResponsavel' => "Secretaria Windows",
            'dataPublicacao' => (new \DateTime())->format('Y-m-d\TH:i:sP'),
            'valorEstimado' => mt_rand(0, 1000000000) / 100
        ];

        $this->client->request(
            'POST',
            '/api/licitacoes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testFindLicitacaoById(): void {

        $licitacao = new Licitacao();
        $licitacao->setTitulo('Licitação Teste');
        $licitacao->setOrgaoResponsavel('Secretaria de Testes');
        $licitacao->setNumeroEdital("TEST-2025/001");
        $licitacao->setDataPublicacao(new \DateTime('2025-08-15T10:00:00-03:00'));
        $licitacao->setValorEstimado(15000);

        $this->entityManager->persist($licitacao);
        $this->entityManager->flush();

        $this->client->request('GET', 'api/licitacoes/' . $licitacao->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($licitacao->getId(), $data['id']);
        $this->assertEquals('Licitação Teste', $data['titulo']);
        $this->assertEquals('Secretaria de Testes', $data['orgaoResponsavel']);
        $this->assertEquals("TEST-2025/001", $data['numeroEdital']);
        $this->assertEquals('2025-08-15', substr($data['dataPublicacao']['date'], 0, 10));
        $this->assertEquals(15000, $data['valorEstimado']);
    }

    public function testUpdateLicitacao(): void {

        $licitacao = new Licitacao();
        $licitacao->setTitulo('Nova licitação');
        $licitacao->setNumeroEdital('EDT-001');
        $licitacao->setOrgaoResponsavel('Secretaria de Obras');
        $licitacao->setDataPublicacao(new \DateTime('2025-08-01T09:00:00-03:00'));
        $licitacao->setValorEstimado(200000);

        $licitacaoService = $this->createMock(LicitacaoService::class);
        $licitacaoService->method('atualizaLicitacao')->willReturn($licitacao);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $repository = $this->createMock(LicitacaoRepository::class);

        $controller = $this->getMockBuilder(LicitacoesController::class)
            ->setConstructorArgs([$licitacaoService, $validator, $repository])
            ->onlyMethods(['json'])
            ->getMock();

        $controller->method('json')->willReturnCallback(function ($data, $status = 200) {
            return new JsonResponse($data, $status);
        });

        $json = json_encode([
            'titulo' => 'Nova licitação',
            'numeroEdital' => 'EDT-001',
            'orgaoResponsavel' => 'Secretaria de Obras',
            'dataPublicacao' => '2025-08-01T09:00:00-03:00',
            'valorEstimado' => 200000
        ]);
        $request = new Request([], [], [], [], [], [], $json);

        $response = $controller->atualiza(1, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Nova licitação', $data['titulo']);
        $this->assertEquals('Secretaria de Obras', $data['orgaoResponsavel']);
        $this->assertEquals('EDT-001', $data['numeroEdital']);
        $this->assertEquals(200000, $data['valorEstimado']);
    }

    protected function tearDown(): void {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}