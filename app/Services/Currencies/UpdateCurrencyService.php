<?php


namespace App\Services\Currencies;


use App\Repositories\BankRepository;

class UpdateCurrencyService
{
    private BankRepository $bankRepository;

    public function __construct()
    {
        $this->bankRepository = new BankRepository();
    }

    public function update()
    {
        //Check if database already has currencies
        if ($this->bankRepository->xml2Array() === 'not empty')
        {
            $this->bankRepository->update();
        }
    }
}
