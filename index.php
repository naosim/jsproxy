<?php
require 'vendor/autoload.php';
require_once 'domain.php';
require_once 'infra.php';
// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
]];
$app = new \Slim\App($config);

function createUrl($paramUrl): Url {
    if (php_sapi_name() == 'cli-server') {
        // built-in server bugfix
        return new Url('https://' . str_replace('_', '.', $paramUrl));
    } else {
        return new Url('https://' . $paramUrl);
    }
}

//gist_githubusercontent_com/naosim/9bee1004c76994752d21800777d2b09e/raw/DisplayOutMonitor_js
$app->get('/web/{url:.*}', function ($request, $response, $args) {
    $service = new FindWebService(new CurlRepositoryImpl(), new CacheRepositoryImpl());

    $url = createUrl($args['url']);
    $body = $service->get($url);

    $response = $response->withHeader('Content-type', 'text/javascript');
    $response->getBody()->write($body->getValue());
    return $response;
});

$app->get('/cache/{url:.*}', function ($request, $response, $args) {
    $service = new FindCacheService(new CurlRepositoryImpl(), new CacheRepositoryImpl());

    $url = createUrl($args['url']);
    $body = $service->get($url);

    $response = $response->withHeader('Content-type', 'text/javascript');
    $response->getBody()->write($body->getValue());
    return $response;
});

// Run app
$app->run();
