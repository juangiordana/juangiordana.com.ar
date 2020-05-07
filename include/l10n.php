<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

putenv('LC_ALL=' . $app['locale']);

setlocale(LC_ALL, $app['locale']);

date_default_timezone_set($app['timezone']);
