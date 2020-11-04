<?php


class Import
{
    public function show()
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

        return require_once __DIR__ . '/IndexView.php';
    }

    public function xml2Array()
    {
        $currencies = query()
            ->select('*')
            ->from('currencies')
            ->execute()
            ->fetchAllAssociative();
        if (!empty($currencies)) {
            return;
        }
        $strContents = file_get_contents('ecb.xml');
        $strData = Xml2Array($strContents);

        for ($i = 0; $i < 30; $i++) {
            $id = ($strData['CRates']['Currencies']['Currency'][$i]['ID']);
            $rate = ($strData['CRates']['Currencies']['Currency'][$i]['Rate']);
            query()
                ->insert('currencies')
                ->values([
                    'currency_id' => ':currencyId',
                    'rate' => ':rate',
                ])
                ->setParameters([
                    'currencyId' => $id,
                    'rate' => $rate
                ])
                ->execute();
        }
    }
}
