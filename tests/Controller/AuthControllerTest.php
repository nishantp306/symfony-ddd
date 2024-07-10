<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testLoginSuccess()
    {
        $this->client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'testuser',
            'password' => 'password123'
        ]));

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEmpty($responseData['token']);

        return $responseData['token'];
    }

    public function testLoginInvalidCredentials()
    {
        $this->client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'nonexistentuser',
            'password' => 'invalidpassword'
        ]));

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
