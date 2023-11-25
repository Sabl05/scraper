<?php
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector;

require 'vendor/autoload.php';

$url = 'http://www.walmart.com';
$uri = '/ip/Time-and-Tru-Women-s-Genuine-Suede-Boots-Wide-Width-Available/2846729825';

$userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0';
$headers = [
    'User-Agent'      => $userAgent,
    'Accept-Encoding' => 'gzip, deflate, br',
];

// Set http client conf
$client = new Client([
    'base_uri'        => $url,
    'timeout'         => 0,
    'allow_redirects' => true,
]);

try {
    $response = $client->get($uri, ['headers' => $headers]);
    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 200) {
        $body = $response->getBody()->getContents();
    } else {
        echo "Unexpected status code: $statusCode";
    }
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "RequestException: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}

// Filter elements by id
$crawler = new Crawler($body);
$filtered = $crawler->filter('#__NEXT_DATA__');
$catsHTML = $filtered
    ->each(function (Crawler $node) {
        return $node->html();
    });
unset($crawler);

// Get product list from json
$product_list = json_decode($catsHTML[0], true);
$product_list = $product_list['props']['pageProps']['initialData']['data']['product']['variantsMap'];

// Build response
$response = [];

foreach($product_list as $product) {
    $template['availabilityStatus'] = $product['availabilityStatus'] ?? null;
    $template['variants'] = $product['variants'] ?? null;
    $template['price'] = $product['priceInfo']['currentPrice']['price'] ?? null;
    $template['price_currency'] = $product['priceInfo']['currentPrice']['currencyUnit'] ?? null;

    $response[] = $template;
    unset($template);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);