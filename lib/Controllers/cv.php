<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$template = $app['template'] . DIRECTORY_SEPARATOR . basename(__FILE__, '.php') . '.twig';

$templateVars = [
    'age' => floor(($_SERVER['REQUEST_TIME'] - mktime(15, 30, 0, 4, 14, 1981)) / 31536000)
];
