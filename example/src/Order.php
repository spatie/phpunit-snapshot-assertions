<?php

namespace App;

class Order
{
    /** @var int */
    private $id;

    /** @var string */
    private $email;

    /** @var bool */
    private $paid;

    /** @var \App\OrderLine[] */
    private $orderLines;

    public function __construct(int $id, string $email, bool $paid, array $orderLines)
    {
        $this->id = $id;
        $this->email = $email;
        $this->paid = $paid;
        $this->orderLines = $orderLines;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function paid(): bool
    {
        return $this->paid;
    }

    public function orderLines(): array
    {
        return $this->orderLines;
    }
}
