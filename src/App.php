<?php

namespace Shortener;

use Nette\Database\Connection;
use Nette\Database\ConnectionException;

class App
{
    /**
     * Stores the database layer.
     *
     * @var Nette\Database\Connection
     */
    private $database;

    /**
     * Undocumented function
     */
    public function __construct()
    {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}";

        try {
            $this->database = new Connection($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        } catch (ConnectionException $e) {
            die($e->getMessage());
        }
    }

    public function run()
    {
        if (isset($_GET['url'])) {

        }
    }
}
