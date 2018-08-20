<?php namespace App\Core;


/**
 * Class Route
 * @package App\Core
 */
class Route extends Singleton
{
    /**
     * Set Controllers classes namespace
     */
    const NAME_SPACE = 'App\\Controllers\\';

    /**
     * Route list with parameters
     *
     * @var array
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * Current url address
     *
     * @var string
     */
    protected $url = '';

    /**
     * Request method
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Current request parameters
     * @var bool
     */
    protected $action = false;

    /**
     * Request source type
     *
     * @var bool
     */
    public $isAjax;

    /**
     * Route constructor.
     */
    protected function __construct()
    {
        $this->setIsAjax();
    }

    /**
     * Add route into route list
     *
     * @param string $method
     * @param string $route
     * @param array $params
     */
    public function add(string $method, string $route, array $params = [])
    {
        $params['url'] = $route;
        $route = preg_replace('/\//', '\\/', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$method][$route] = $params;
    }

    /**
     * Add GET method route into route list
     *
     * @param string $route
     * @param array $params
     */
    public function get(string $route, array $params = [])
    {
        $this->add('GET', $route, $params);
    }

    /**
     * Add POST method route into route list
     *
     * @param string $route
     * @param array $params
     */
    public function post(string $route, array $params = [])
    {
        $this->add('POST', $route, $params);
    }

    /**
     * Resolve route controller with method and run it.
     *
     * @return mixed
     * @throws \Exception
     */
    public function load()
    {
        //Prepare all needed data for action resolve
        $this->prepare();

        //get controller and check ir it exists
        $controller = $this->action['controller'] ?? null;
        $controller = self::NAME_SPACE . $controller . 'Controller';
        if (class_exists($controller)) {
            $controller_object = new $controller();
            //get method and check if it exists in controller
            $method = $this->action['method'] ?? null;
            if (method_exists($controller_object, $method)) {
                //run route method
                return $controller_object->$method();
            } else {
                throw new \Exception("Method $method in controller $controller does not exists");
            }
        } else {
            throw new \Exception("Controller class $controller not found");
        }
    }

    /**
     * Prepare all needed data for action resolve
     *
     * @throws \Exception
     */
    private function prepare()
    {
        $this->resolveUrl();
        $this->resolveMethod();
        $this->resolveAction();
    }

    /**
     *  Resolve url from request
     */
    private function resolveUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        if ($url != '') {
            $parts = explode('?', $url, 2);
            $url = preg_replace('/^' . preg_quote('/', '/') . '/', '', $parts[0]);
        }
        $this->url = $url;
    }

    /**
     *
     */
    private function resolveMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Resolve route parameters
     *
     * @throws \Exception
     */
    private function resolveAction()
    {
        if (!isset($this->routes[$this->method])) {
            throw new \Exception('Bad method');
        }

        foreach ($this->routes[$this->method] as $route => $params) {
            if (preg_match($route, $this->url)) {
                $this->action = $params;
            }
        }

        if (!$this->action) {
            throw new \Exception('Route not found');
        }
    }

    /**
     * Get url by route name.
     *
     * @param string $routeName
     * @return null|string
     */
    public function url(string $routeName): ?string
    {
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route => $params) {
                if (isset($params['name']) && $params['name'] === $routeName) {
                    return $params['url'] ?? '';
                }
            }
        }

        return null;
    }

    /**
     *  Resolve is request is sent using ajax
     *  Ajax method sets HTTP_X_REQUESTED_WITH header
     *  This is a hack way but it works on newest browsers
     *  More info https://stackoverflow.com/questions/18260537/how-to-check-if-the-request-is-an-ajax-request-with-php
     */
    public function setIsAjax()
    {
        $this->isAjax = 'XMLHttpRequest' == ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    }
}
