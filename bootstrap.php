<?php

require 'vendor/autoload.php';

/**
 * Load the .env file
 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Load our app.
 */
$app = new Shortener\App($dotenv);
$app->run();