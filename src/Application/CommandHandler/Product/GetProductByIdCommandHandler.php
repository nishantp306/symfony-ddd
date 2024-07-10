<?php

namespace App\Application\CommandHandler\Product;

use App\Application\Command\Product\GetProductByIdCommand;
use App\Domain\Model\Repository\ProductRepository;
use Exception;

class GetProductByIdCommandHandler
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param GetProductByIdCommand $command
     * @return array|null
     * @throws Exception
     */
    public function handle(GetProductByIdCommand $command): ?array
    {
        $productId = $command->getProductId();

        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new Exception('Product not found.');
        }

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
        ];
    }
}
