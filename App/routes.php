<?php

$router = \App\Core\Route::getInstance();

$router->add('GET', '', ['controller' => 'Home', 'method' => 'index', 'name' => 'home']);
$router->add('POST', 'messages', ['controller' => 'Message', 'method' => 'store', 'name' => 'storeMessage']);

