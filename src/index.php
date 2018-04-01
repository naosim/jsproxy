<?php
require dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/domain.php';
require_once dirname(__FILE__) . '/infra.php';
// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
]];
$app = new \Slim\App($config);

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
