<?php


namespace App\Repositories;

class DatabaseRepository
{
    public function getAllCurrencies(): array
    {
        $currencyQuery = query()
            ->select('*')
            ->from('currencies')
            ->execute()
            ->fetchAllAssociative();

        $currencies = [];

        foreach ($currencyQuery as $currency) {
            $currencies[] = $currency['currency_id'] . ':' . $currency['rate'];
        }

        return $currencies;
    }
}
