<?php
require_once dirname(dirname(dirname( __DIR__ ))) . '/wp-load.php';
require_once __DIR__ . '/Config.php';
$clearvisioBookerConfig = new Clearvisio_Booker_Config();

$apiPath = $clearvisioBookerConfig->get('api_url');
$apiKey = $clearvisioBookerConfig->get('api_key');

$headers = ['X-AUTH-API-TOKEN: ' . $apiKey];
foreach (getallheaders() as $key => $value) {
    $headers[] = $key . ': ' . $value;
}

$result = @file_get_contents(
    $apiPath . $_SERVER['PATH_INFO'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''),
    false,
    stream_context_create([
        'http' => [
            'header' => $headers,
            'method' => $_SERVER['REQUEST_METHOD'],
            'content' => file_get_contents('php://input')
        ],
    ])
);

foreach ((isset($http_response_header) && is_array($http_response_header)) ? $http_response_header : [] as $header) {
    header($header);
}

echo $result;
