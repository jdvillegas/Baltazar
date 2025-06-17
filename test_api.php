<?php
require __DIR__.'/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
    'verify' => false,
    'http_errors' => false,
    'timeout' => 10
]);

$url = 'https://consultaprocesos.ramajudicial.gov.co:448/api/v2/Procesos/Consulta/NumeroRadicacion';
$params = [
    'numero' => '76001310500120180064401',
    'SoloActivos' => 'false',
    'pagina' => 1
];

try {
    $response = $client->get($url, [
        'query' => $params,
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept' => 'application/json',
        ]
    ]);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Headers: \n";
    print_r($response->getHeaders());
    echo "\nBody: \n" . $response->getBody();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse')) {
        echo "Response: " . $e->getResponse()->getBody();
    }
}
