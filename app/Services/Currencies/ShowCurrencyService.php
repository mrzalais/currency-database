<?php


namespace App\Services\Currencies;

use App\Models\Currency;
use App\Repositories\DatabaseRepository;

class ShowCurrencyService
{
    private DatabaseRepository $databaseRepository;

    public function __construct()
    {
        $this->databaseRepository = new DatabaseRepository();
    }

    public function execute(): array
    {
        return $this->databaseRepository->getAllCurrencies();
    }
}
