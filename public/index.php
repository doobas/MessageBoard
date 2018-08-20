<?php
/*
 * Display all errors
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
 * Require all autoload classes
 */
require_once __DIR__ . '/../vendor/autoload.php';

//Include helper functions
include __DIR__ . '/../App/Core/helpers.php';
//Include routes
include __DIR__ . '/../App/routes.php';

//Resolve route and load content
echo \App\Core\Route::getInstance()->load();
