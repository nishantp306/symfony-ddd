<?php

namespace App\Infrastructure\Controller;

use App\Application\Command\Product\CreateProductCommand;
use App\Application\Command\Product\DeleteProductCommand;
use App\Application\Command\Product\GetAllProductsCommand;
use App\Application\Command\Product\GetProductByIdCommand;
use App\Application\Command\Product\UpdateProductCommand;
use App\Application\CommandHandler\Product\CreateProductCommandHandler;
use App\Application\CommandHandler\Product\DeleteProductCommandHandler;
use App\Application\CommandHandler\Product\GetAllProductsCommandHandler;
use App\Application\CommandHandler\Product\GetProductByIdCommandHandler;
use App\Application\CommandHandler\Product\UpdateProductCommandHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted("ROLE_USER")]
class ProductController extends AbstractController
{

    private CreateProductCommandHandler $createProductHandler;
    private GetAllProductsCommandHandler $getAllProductsCommandHandler;
    private GetProductByIdCommandHandler $getProductByIdCommandHandler;
    private DeleteProductCommandHandler $deleteProductCommandHandler;
    private UpdateProductCommandHandler $updateProductCommandHandler;

    public function __construct(CreateProductCommandHandler $createProductHandler, GetAllProductsCommandHandler $getAllProductsCommandHandler, GetProductByIdCommandHandler $getProductByIdCommandHandler, DeleteProductCommandHandler $deleteProductCommandHandler, UpdateProductCommandHandler $updateProductCommandHandler)
    {
        $this->createProductHandler = $createProductHandler;
        $this->getAllProductsCommandHandler = $getAllProductsCommandHandler;
        $this->getProductByIdCommandHandler = $getProductByIdCommandHandler;
        $this->deleteProductCommandHandler = $deleteProductCommandHandler;
        $this->updateProductCommandHandler = $updateProductCommandHandler;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateProductCommand(
            $data['name'],
            $data['price'],
            $data['description'],
            $this->getUser()->getId()
        );

        $createdProduct = $this->createProductHandler->__invoke($command);

        return $this->json(['product' => $createdProduct, 'message' => 'Product created successfully'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $command = new GetAllProductsCommand($page, $limit);
        $products = $this->getAllProductsCommandHandler->handle($command);

        return $this->json(['products' => $products]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/products/{id}', name: 'get_product_by_id', methods: ['GET'])]
    public function getProductById(int $id): JsonResponse
    {
        $command = new GetProductByIdCommand($id);
        $product = $this->getProductByIdCommandHandler->handle($command);

        return $this->json(['product' => $product]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(int $id): JsonResponse
    {
        $command = new DeleteProductCommand($id);
        $this->deleteProductCommandHandler->handle($command);

        return $this->json(['message' => 'Product deleted successfully']);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new UpdateProductCommand(
            $id,
            $data['name'],
            $data['price'],
            $data['description']
        );

        $updatedProduct = $this->updateProductCommandHandler->handle($command);

        return $this->json(['product' => $updatedProduct, 'message' => 'Product updated successfully']);
    }
}
