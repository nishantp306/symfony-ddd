<?php

namespace App\Application\CommandHandler\Product;

use App\Application\Command\Product\CreateProductCommand;
use App\Domain\Model\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class CreateProductCommandHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CreateProductCommand $command
     * @return array
     */
    public function __invoke(CreateProductCommand $command): array
    {
        $product = new Product();
        $product->setName($command->getName());
        $product->setPrice($command->getPrice());
        $product->setDescription($command->getDescription());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription()
        ];
    }
}
