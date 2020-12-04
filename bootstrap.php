<?php

require 'vendor/autoload.php';

use Nette\Database\Connection;
use Nette\Database\ConnectionException;

/**
 * Load the .env file
 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

try {
    $dotenv->load();
} catch(Exception $e) {
    die("Error - missing .env file");
}

/**
 * Connect to the DB.
 */
$dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}";

try {
    $connection = new Connection($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
} catch (ConnectionException $e) {
    die($e->getMessage());
}