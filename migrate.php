<?php

require './bootstrap.php';

use Shortener\App;

$app = new App($connection);
$app->migrate();