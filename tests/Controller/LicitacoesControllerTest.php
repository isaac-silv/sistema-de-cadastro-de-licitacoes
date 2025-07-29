<?php
namespace App\Tests\Controller;

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
            'dataPublicacao' => (new \DateTime())->format('Y-m-d\TH:i:sP')
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}