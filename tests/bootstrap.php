<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/.env.test')) {
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env.test');
}

// Agora já temos DATABASE_URL carregada
$kernel = new \App\Kernel('test', true);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine')->getManager();
$metadata = $entityManager->getMetadataFactory()->getAllMetadata();

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
$schemaTool->dropDatabase();
$schemaTool->createSchema($metadata);

