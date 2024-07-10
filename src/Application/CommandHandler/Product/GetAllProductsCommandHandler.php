<?php

namespace App\Application\CommandHandler\Product;

use App\Application\Command\Product\GetAllProductsCommand;
use App\Domain\Model\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;

class GetAllProductsCommandHandler
{
    private ProductRepository $productRepository;
    private PaginatorInterface $paginator;

    public function __construct(ProductRepository $productRepository, PaginatorInterface $paginator)
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param GetAllProductsCommand $command
     * @return array
     */
    public function handle(GetAllProductsCommand $command): array
    {
        $page = $command->getPage();
        $limit = $command->getLimit();

        $query = $this->productRepository->findAll();

        $paginatedProducts = $this->paginator->paginate(
            $query,
            $page,
            $limit
        );

        $products = [];
        foreach ($paginatedProducts as $product) {
            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }

        return $products;
    }
}
