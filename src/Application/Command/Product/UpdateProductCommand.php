<?php

namespace App\Application\Command\Product;

class UpdateProductCommand
{
    private int $productId;
    private string $name;
    private float $price;
    private string $description;

    public function __construct(int $productId, string $name, float $price, string $description)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
