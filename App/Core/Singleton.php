<?php namespace App\Core;

/**
 * Singleton class to implement other classes that can be load once.
 *
 * Class Singleton
 * @package App\Core
 */
class Singleton
{
    private static $instances = array();

    protected function __construct() { }

    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }

    private function __clone() { }

    private function __wakeup() { }
}
