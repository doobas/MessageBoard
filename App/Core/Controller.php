<?php namespace App\Core;

/**
 * Class Controller
 * @package App\Core
 */
abstract class Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Route
     */
    protected $route;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->request = Request::getInstance();
        $this->route = Route::getInstance();
    }
}
