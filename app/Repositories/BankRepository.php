<?php


namespace App\Repositories;

use App\Models\Currency;

require_once 'Import.php';

class BankRepository
{
    public function xml2Array()
    {
        $currenciesQuery = query()
            ->select('*')
            ->from('currencies')
            ->execute()
            ->fetchAllAssociative();
        if (!empty($currenciesQuery)) {
            return 'not empty';
        }

        $currencies = $this->getFromBank();

        foreach ($currencies as $currency) {
            query()
                ->insert('currencies')
                ->values([
                    'currency_id' => ':currencyId',
                    'rate' => ':rate'
                ])
                ->setParameters([
                    'currencyId' => $currency->id(),
                    'rate' => $currency->rate()
                ])
                ->execute();
        }
    }

    public function getFromBank(): array
    {
        $strContents = file_get_contents('https://www.bank.lv/vk/ecb.xml');
        $strData = Xml2Array($strContents);

        $currencies = [];

        for ($i = 0; $i < 30; $i++) {
            $id = ($strData['CRates']['Currencies']['Currency'][$i]['ID']);
            $rate = ($strData['CRates']['Currencies']['Currency'][$i]['Rate']);

            $currencies[] = new Currency(
                $id,
                $rate
            );
        }

        return $currencies;
    }

    public function update()
    {
        $currencies = $this->getFromBank();

        foreach ($currencies as $currency) {
            query()
                ->update('currencies')
                ->set('rate', $currency->rate())
                ->where('currency_id = :currency_id')
                ->setParameter('currency_id', $currency->id())
                ->execute();
        }
    }
}
