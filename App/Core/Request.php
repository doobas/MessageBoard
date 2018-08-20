<?php namespace App\Core;


/**
 * Class Request
 * @package App\Core
 */
class Request extends Singleton
{
    /**
     * @var array
     */
    protected $request;

    /**
     * Request constructor.
     */
    protected function __construct()
    {
        $this->request = $_REQUEST;
    }

    /**
     * Get data from request through magic call
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->request)) {
            return $this->request[$name];
        }

        return null;
    }

    /**
     * Return all request data
     *
     * @return array
     */
    public function all(): array
    {
        return $this->request;
    }

    /**
     * Get only requested parameters from request data
     *
     * @param mixed ...$keys
     * @return array
     */
    public function only(...$keys): array
    {
        $return = [];
        foreach ($keys as $key) {
            $return[$key] = $this->$key;
        }
        return $return;
    }
}
