<?php
namespace App\Tests\Controller;

use App\Entity\Licitacao;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LicitacoesControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Configuração específica para Windows
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        // Limpa os dados antes de cada teste
        $this->entityManager->getConnection()->executeStatement('TRUNCATE TABLE licitacoes RESTART IDENTITY CASCADE');
    }

    public function testCreateLicitacao()
    {
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

    public function testListarLicitacoes(): void
    {

        $licitacao1 = new Licitacao();
        $licitacao1->setTitulo('Licitação 1');
        $licitacao1->setOrgaoResponsavel('Secretaria de desenvolvimento urbano');
        $licitacao1->setNumeroEdital("SDU-2025/001");
        $licitacao1->setDataPublicacao(new \DateTime('2025-08-01T09:00:00-03:00'));
        $licitacao1->setValorEstimado(10000);

        $licitacao2 = new Licitacao();
        $licitacao2->setTitulo('Licitação 2');
        $licitacao2->setOrgaoResponsavel('Secretaria de Educação');
        $licitacao2->setNumeroEdital("SDE-2025/001");
        $licitacao2->setDataPublicacao(new \DateTime('2025-08-01T09:00:00-03:00'));
        $licitacao2->setValorEstimado(20000);

        $this->entityManager->persist($licitacao1);
        $this->entityManager->persist($licitacao2);
        $this->entityManager->flush();


        $this->client->request('GET', '/api/licitacoes');


        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $data = json_decode($this->client->getResponse()->getContent(), true);


        $this->assertCount(2, $data);


        $this->assertEquals('Licitação 1', $data[0]['titulo']);
        $this->assertEquals('Secretaria de desenvolvimento urbano', $data[0]['orgaoResponsavel']);
        $this->assertEquals('SDU-2025/001', $data[0]['numeroEdital']);
        $this->assertEquals('2025-08-01', substr($data[0]['dataPublicacao']['date'], 0, 10));
        $this->assertEquals(10000, $data[0]['valorEstimado'] ?? 0);
        $this->assertEquals('Licitação 2', $data[1]['titulo']);
        $this->assertEquals('Secretaria de Educação', $data[1]['orgaoResponsavel']);

    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}