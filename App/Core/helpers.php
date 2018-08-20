<?php

use App\Core\View;

/**
 * Dump and Die helper function
 *
 * @param $value mixed
 */
function dd($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
    die;
}

/**
 * Dump helper function
 *
 * @param $value mixed
 */
function d($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

/**
 * Load View class shortener helper function
 *
 * @param $template string
 * @return View
 */
function view(string $template): View
{
    return (new View($template));
}

/**
 * Redirect response helper function
 *
 * @param $url
 * @param int $statusCode
 */
function redirect(string $url, int $statusCode = 200)
{
    header('Location: ' . $url, true, $statusCode);
    die();
}
