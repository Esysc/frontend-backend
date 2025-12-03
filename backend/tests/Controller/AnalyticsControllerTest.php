<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\After;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Route as RouteEntity;
use App\Entity\User;

class AnalyticsControllerTest extends WebTestCase
{
    private static bool $initialized = false;

    private function getAuthHeaders(KernelBrowser $client, string $username = 'testuser', string $password = 'testpass'): array
    {
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();

        $existing = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$existing) {
            $user = new User($username, '');
            $user->setPassword($container->get('security.user_password_hasher')->hashPassword($user, $password));
            $em->persist($user);
            $em->flush();
        }

        $client->request('POST', '/api/v1/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return ['HTTP_Authorization' => 'Bearer ' . $data['token']];
    }

    private function ensureSetup(): void
    {
        if (self::$initialized) {
            return;
        }

        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Use SchemaTool to create the schema directly
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        // create a sample route entry for analytics
        $route = new RouteEntity('MX', 'ZW', 'ANA-TEST', 10.5, ['MX','CGE','ZW']);
        $entityManager->persist($route);
        $entityManager->flush();

        self::$initialized = true;
    }

    public function testGetAnalyticsReturnsAggregatedResult(): void
    {
        $client = static::createClient();
        $this->ensureSetup();
        $headers = $this->getAuthHeaders($client);

        $client->request('GET', '/api/v1/stats/distances', [], [], $headers);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(),
            'Analytics endpoint failed with: ' . $response->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('items', $data);
        $this->assertIsArray($data['items']);

        // Find our analytic code in items
        $found = false;
        foreach ($data['items'] as $item) {
            if (($item['analyticCode'] ?? '') === 'ANA-TEST') {
                $found = true;
                $this->assertTrue($item['totalDistanceKm'] >= 10.5);
            }
        }

        $this->assertTrue($found, 'Expected analytic code ANA-TEST in response items');
    }

     #[After]
    public function __internalDisableErrorHandler(): void
    {
        @restore_exception_handler();
    }
}
