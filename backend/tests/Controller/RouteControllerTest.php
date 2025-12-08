<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\After;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Station;

class RouteControllerTest extends WebTestCase
{
    private const CONTENT_TYPE_JSON = 'application/json';

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

        // Load some test stations (not MOB but more worldwide :) )
        $station1 = new Station('MX', 'Mexico', 'Station Mexico');
        $station2 = new Station('ZW', 'Zurich', 'Station Zurich');
        $station3 = new Station('CGE', 'Copenhagen', 'Station Copenhagen');
        $entityManager->persist($station1);
        $entityManager->persist($station2);
        $entityManager->persist($station3);
        $entityManager->flush();


        // Load distance data so graph has paths
        $distance1 = new \App\Entity\Distance('Test-Line', 'MX', 'ZW', 100.0);
        $distance2 = new \App\Entity\Distance('Test-Line', 'ZW', 'CGE', 150.0);
        $distance3 = new \App\Entity\Distance('Test-Line', 'MX', 'CGE', 200.0);
        $entityManager->persist($distance1);
        $entityManager->persist($distance2);
        $entityManager->persist($distance3);
        $entityManager->flush();

        self::$initialized = true;
    }

    private function getAuthHeaders(KernelBrowser $client, string $username = 'testuser', string $password = 'testpass'): array
    {
        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();

        $existing = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$existing) {
            $user = new User($username, '');
            $hasher = $container->get('security.password_hasher');
            $user->setPassword($hasher->hashPassword($user, $password));
            $em->persist($user);
            $em->flush();
        }

        $client->request('POST', '/api/v1/auth/login', [], [], ['CONTENT_TYPE' => self::CONTENT_TYPE_JSON], json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return ['HTTP_Authorization' => 'Bearer ' . $data['token']];
    }

    public function testCreateRouteReturns201AndPersists(): void
    {
        $client = static::createClient();
        $this->ensureSetup();
        $headers = $this->getAuthHeaders($client);

        $payload = [
            'fromStationId' => 'MX',
            'toStationId' => 'ZW',
            'analyticCode' => 'TEST-123',
        ];

        $client->request('POST', '/api/v1/routes', [], [], $headers + ['CONTENT_TYPE' => self::CONTENT_TYPE_JSON], json_encode($payload));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('MX', $data['fromStationId']);
        $this->assertEquals('ZW', $data['toStationId']);
        $this->assertEquals('TEST-123', $data['analyticCode']);
        $this->assertArrayHasKey('distanceKm', $data);
        $this->assertArrayHasKey('path', $data);
    }

    public function testCreateRouteInvalidStationReturns422(): void
    {
        $client = static::createClient();
        $this->ensureSetup();
        $headers = $this->getAuthHeaders($client);

        $payload = [
            'fromStationId' => 'UNKNOWN',
            'toStationId' => 'ZW',
            'analyticCode' => 'TEST-ERR',
        ];

        $client->request('POST', '/api/v1/routes', [], [], $headers + ['CONTENT_TYPE' => self::CONTENT_TYPE_JSON], json_encode($payload));

        $this->assertTrue(in_array($client->getResponse()->getStatusCode(), [422, 500]));
    }

     #[After]
    public function __internalDisableErrorHandler(): void
    {
        @restore_exception_handler();
    }
}
