<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$app->get('/pizzas', function (HttpRequest $request, HttpResponse $response) {
    $response->json([]);
});
