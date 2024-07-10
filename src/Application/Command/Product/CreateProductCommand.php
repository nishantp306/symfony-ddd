<?php

namespace App\Application\Command\Product;

class CreateProductCommand
{
    private string $name;
    private float $price;

    private string $description;
    private int $userId;

    public function __construct(string $name, float $price, string $description, int $userId)
    {
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->userId = $userId;
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

    public function getUserId(): int
    {
        return $this->userId;
    }
}
