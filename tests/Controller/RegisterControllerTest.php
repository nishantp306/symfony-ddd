<?php

namespace App\Tests\Controller;

use App\Application\Command\Registration\RegisterUserCommand;
use App\Application\CommandHandler\Registration\RegisterUserCommandHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegisterControllerTest extends WebTestCase
{
    private $client;
    private $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
    }

    public function testRegisterSuccess()
    {
        $mockHandler = $this->createMock(RegisterUserCommandHandler::class);
        $mockHandler->expects($this->once())->method('handle')->willReturn(null);
        $this->container->set(RegisterUserCommandHandler::class, $mockHandler);

        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'Password123!'
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['message' => 'User registered successfully']), $this->client->getResponse()->getContent());
    }

    public function testRegisterEmailExists()
    {
        $mockHandler = $this->createMock(RegisterUserCommandHandler::class);
        $mockHandler->expects($this->once())->method('handle')->will($this->throwException(new \Exception('Email already exists.')));
        $this->container->set(RegisterUserCommandHandler::class, $mockHandler);

        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'Password123!'
        ]));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Email already exists.']), $this->client->getResponse()->getContent());
    }

    public function testRegisterUsernameExists()
    {
        $mockHandler = $this->createMock(RegisterUserCommandHandler::class);
        $mockHandler->expects($this->once())->method('handle')->will($this->throwException(new \Exception('Username already exists.')));
        $this->container->set(RegisterUserCommandHandler::class, $mockHandler);

        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'Password123!'
        ]));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Username already exists.']), $this->client->getResponse()->getContent());
    }

    public function testRegisterWeakPassword()
    {
        $this->client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'weakpass'
        ]));

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['errors' => ['Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.']]), $this->client->getResponse()->getContent());
    }
}
