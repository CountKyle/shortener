<?php

require '../bootstrap.php';

use Shortener\App;

$app = new App($connection);

/**
 * Attempts to store a url and return a shortcode. e.g. shortener.test/shorten?url=http://www.google.com
 * Else, attempts to search for a shortcode and perform a redirect. e.g. shortener.test/qkt880
 */
if (array_key_exists('request', $_GET) && $_GET['request'] === 'shorten' && array_key_exists('url', $_GET)) {
    $app->processLongUrl($_GET['url']);
} else if (!empty($_GET['request'])) {
    $app->processShortCode($_GET['request']);
}
