<?php

namespace App\Tests\Controller;

use App\Domain\Model\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthControllerTest extends WebTestCase
{
    private $client;
    private $email = 'testuser@example.com';
    private $username = 'testuser';
    private $password = 'Test@123';


    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->registerUserIfNotExists();
    }

    private function registerUserIfNotExists()
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
        ]));

        $response = $this->client->getResponse();
        if ($response->getStatusCode() !== JsonResponse::HTTP_CREATED) {
            throw new \Exception('Failed to register user for testing: ' . $response->getContent());
        }
    }

    public function testLoginSuccess()
    {
        $this->client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'testuser',
            'password' => 'Test@123'
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
