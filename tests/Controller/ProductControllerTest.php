<?php

namespace App\Tests\Controller;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductControllerTest extends WebTestCase
{
    private $client;
    private $accessToken;
    private $createdProductId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->accessToken = $this->getTokenFromLogin();
        $this->testCreateProduct();
    }

    private function getTokenFromLogin(): string
    {
        $this->client->request('POST', '/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'testuser',
            'password' => 'password123'
        ]));

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Check if login was successful and token exists
        if ($response->getStatusCode() === JsonResponse::HTTP_OK && isset($responseData['token'])) {
            return $responseData['token'];
        } else {
            throw new \Exception('Failed to retrieve JWT token from login.');
        }
    }

    public function testCreateProduct()
    {
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken
            ],
            json_encode([
                'name' => 'Test Product',
                'price' => 100,
                'description' => 'This is a test product.'
            ]),
            false
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('product', $responseData);
        $this->assertArrayHasKey('id', $responseData['product']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Product created successfully', $responseData['message']);

        $this->createdProductId = $responseData['product']['id'];

        $this->assertNotNull($this->createdProductId);
    }

    public function testGetProducts()
    {
        $this->client->request(
            'GET',
            '/api/products',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('products', $responseData);
    }

    public function testGetProductById()
    {
        $this->assertNotNull($this->createdProductId);

        $this->client->request(
            'GET',
            '/api/products/' . $this->createdProductId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('product', $responseData);
    }

    public function testUpdateProduct()
    {
        $this->assertNotNull($this->createdProductId);
        $this->client->request(
            'PUT',
            '/api/products/' . $this->createdProductId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken],
            json_encode([
                'name' => 'Updated Product Name',
                'price' => 150,
                'description' => 'Updated product description.'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('product', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Product updated successfully', $responseData['message']);
    }

    public function testDeleteProduct()
    {
        $this->assertNotNull($this->createdProductId);
        $this->client->request(
            'DELETE',
            '/api/products/' . $this->createdProductId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Product deleted successfully', $responseData['message']);
    }
}
