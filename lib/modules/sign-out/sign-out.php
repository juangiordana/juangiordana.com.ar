<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if ($act) {
    $userService->logout();
}

$template = $twig->loadTemplate($app['template'] . '/modules/' . basename(__DIR__). '/' . basename(__FILE__, '.php') . '.twig');
