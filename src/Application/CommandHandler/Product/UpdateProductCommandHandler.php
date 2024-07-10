<?php

namespace App\Application\CommandHandler\Product;

use App\Application\Command\Product\UpdateProductCommand;
use App\Domain\Model\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UpdateProductCommandHandler
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UpdateProductCommand $command
     * @return array
     * @throws Exception
     */
    public function handle(UpdateProductCommand $command): array
    {
        $productId = $command->getProductId();
        $name = $command->getName();
        $price = $command->getPrice();
        $description = $command->getDescription();

        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new Exception('Product not found.');
        }

        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);

        $this->entityManager->flush();

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
        ];
    }
}
