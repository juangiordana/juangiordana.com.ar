<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$template = $app['template'] . '/default/' . basename(__FILE__, '.php') . '.twig';
