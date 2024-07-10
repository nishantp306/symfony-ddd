<?php

namespace App\Application\CommandHandler\Product;

use App\Application\Command\Product\DeleteProductCommand;
use App\Domain\Model\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteProductCommandHandler
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param DeleteProductCommand $command
     * @return void
     * @throws Exception
     */
    public function handle(DeleteProductCommand $command): void
    {
        $productId = $command->getProductId();

        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new Exception('Product not found.');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
