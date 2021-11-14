<?php


namespace App\Models;

class Currency
{
    private string $id;
    private float $rate;

    public function __construct(string $id, float $rate)
    {
        $this->id = $id;
        $this->rate = $rate;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function rate(): float
    {
        return $this->rate;
    }
}
