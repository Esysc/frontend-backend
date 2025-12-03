<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\After;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class AuthControllerTest extends WebTestCase
{
    private static bool $initialized = false;

    private function ensureSetup(): void
    {
        if (self::$initialized) {
            return;
        }

        $entityManager = self::getContainer()->get('doctrine')->getManager();

        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        self::$initialized = true;
    }

    public function testRegisterAndLogin(): void
    {
        $client = static::createClient();
        $this->ensureSetup();

        $payload = ['username' => 'integ-user', 'password' => 'pass123'];

        // register
        $client->request('POST', '/api/v1/auth/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // try login
        $client->request('POST', '/api/v1/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

     #[After]
    public function __internalDisableErrorHandler(): void
    {
        @restore_exception_handler();
        @restore_exception_handler();
    }
}
