<?php namespace App;


/**
 * Class Config
 * @package App
 */
class Config
{
    /**
     *  Database host
     */
    const DB_HOST = "127.0.0.1";

    /**
     *  Database username
     */
    const DB_USERNAME = "root";

    /**
     *  Database user password
     */
    const DB_PASSWORD = "root";

    /**
     *  Database name
     */
    const DB_DATABASE = "emotion";

    /**
     *  Directory where view files are saved.
     */
    const VIEW_DIR = __DIR__ . "/Views/";
}
