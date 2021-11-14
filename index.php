<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function database(): Connection
{
    $connectionParams = [
        'dbname' => $_ENV['DB_DATABASE'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
        'host' => $_ENV['DB_HOST'],
        'driver' => 'pdo_mysql',
    ];

    $connection = DriverManager::getConnection($connectionParams);
    $connection->connect();

    return $connection;
}

function query(): QueryBuilder
{
    return database()->createQueryBuilder();
}

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $namespace = '\App\Controllers\\';

    $r->addRoute('GET', '/', $namespace . 'CurrencyController@index');
    $r->addRoute('GET', '/currency', $namespace . 'CurrencyController@show');
});

$httpMethod = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 PAGE NOT FOUND';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo 'METHOD NOT ALLOWED';
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controller, $method] = explode('@', $routeInfo[1]);
        $vars = $routeInfo[2];

        (new $controller)->$method($vars);

        break;
$strContents = file_get_contents('ecb.xml');
$strDatas = Xml2Array($strContents);

$x = 0;
for ($i = 0; $i < 30; $i++) {
    $id = ($strDatas['CRates']['Currencies']['Currency'][$i]['ID']);
    $rate = ($strDatas['CRates']['Currencies']['Currency'][$i]['Rate']);
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

$currencyQuery = query()
    ->select('*')
    ->from('currencies')
    ->execute()
    ->fetchAllAssociative();

$currencies = [];

foreach ($currencyQuery as $currency)
{
    echo $currency['currency_id'];
    echo $currency['rate'];
}

function Xml2Array($contents, $get_attributes = 1, $priority = 'tag')
{
    if (!$contents) return [];

    if (!function_exists('xml_parser_create')) {
        return [];
    }

    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if (!$xml_values) return;

    $xml_array = [];
    $parents = [];
    $opened_tags = [];
    $arr = [];

    $current = &$xml_array;

    $repeated_tag_index = [];
    foreach ($xml_values as $data) {
        unset($attributes, $value);

        extract($data);

        $result = [];
        $attributes_data = [];

        if (isset($value)) {
            if ($priority === 'tag') $result = $value;
            else $result['value'] = $value;
        }

        if (isset($attributes) and $get_attributes) {
            foreach ($attributes as $attr => $val) {
                if ($priority === 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val;
            }
        }

        if ($type === "open") {
            $parent[$level - 1] = &$current;
            if (!is_array($current) or (!array_key_exists($tag, $current))) {
                $current[$tag] = $result;
                if ($attributes_data) $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;

                $current = &$current[$tag];

            } else {

                if (isset($current[$tag][0])) {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                } else {
                    $current[$tag] = array($current[$tag], $result);
                    $repeated_tag_index[$tag . '_' . $level] = 2;

                    if (isset($current[$tag . '_attr'])) {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset($current[$tag . '_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif ($type === "complete") {

            if (!isset($current[$tag])) {
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority === 'tag' and $attributes_data) $current[$tag . '_attr'] = $attributes_data;


            } else {
                if (isset($current[$tag][0]) and is_array($current[$tag])) {

                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                    if ($priority === 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;

                } else {
                    $current[$tag] = array($current[$tag], $result);
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority === 'tag' and $get_attributes) {
                        if (isset($current[$tag . '_attr'])) {

                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }

                        if ($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
            }

        } elseif ($type === 'close') {
            $current = &$parent[$level - 1];
        }
    }

    return ($xml_array);
}
