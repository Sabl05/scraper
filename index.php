<?php

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector;

require 'vendor/autoload.php';

//$uri = 'https://www.walmart.com/ip/Time-and-Tru-Women-s-Genuine-Suede-Boots-Wide-Width-Available/2846729825'; // Тут можно вставить любой урл продукта с walmart

$url = 'http://www.walmart.com';
$uri = '/all-departments';

$userAgent = 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.97 Safari/537.36';
$headers = ['User-Agent' => $userAgent];

$client = new Client([
    'base_uri'        => $url,
    'timeout'         => 0,
    'allow_redirects' => false,
]);

try {
    $response = $client->get($uri, ['headers' => $headers]);
    $body = $response->getBody()->getContents();
    echo $body; // or do something else with the response
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "RequestException: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}