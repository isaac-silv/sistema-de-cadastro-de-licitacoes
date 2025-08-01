<?php
namespace App\Tests\Controller;

use App\Controller\LicitacoesController;
use App\Entity\Licitacao;
use App\Repository\LicitacaoRepository;
use App\Service\LicitacaoService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LicitacoesControllerTest extends WebTestCase {
    private $client;
    private $entityManager;

    protected function setUp(): void {
        $this->client = static::createClient();

        // Configuração específica para Windows
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        // Limpa os dados antes de cada teste
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

    protected function tearDown(): void {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}