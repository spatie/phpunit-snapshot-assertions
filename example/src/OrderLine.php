<?php

namespace App;

class OrderLine
{
    /** @var int */
    private $id;

    /** @var string */
    private $description;

    /** @var int */
    private $unitPrice;

    /** @var int */
    private $quantity;

    public function __construct(int $id, string $description, int $unitPrice, int $quantity)
    {
        $this->id = $id;
        $this->description = $description;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function unitPrice(): int
    {
        return $this->unitPrice;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function totalPrice(): int
    {
        return $this->unitPrice * $this->quantity;
    }
}
