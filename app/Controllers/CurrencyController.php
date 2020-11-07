<?php


namespace App\Controllers;

use App\Services\Currencies\ShowCurrencyService;
use App\Services\Currencies\UpdateCurrencyService;

class CurrencyController
{
    public function index()
    {
        return require_once __DIR__ . '/../Views/IndexView.php';
    }

    public function show()
    {
        (new UpdateCurrencyService())->update();
        $currencies = (new ShowCurrencyService())->execute();
        return require_once __DIR__ . '/../Views/CurrencyView.php';
    }
}
