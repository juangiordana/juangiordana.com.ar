<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

use App\Users\UserService;

/**
 * If a User is logged in we always get what we need through $act.
 */
$act = null;

/**
 * PHP session handler.
 */
if (PHP_SAPI == 'cli') {
    return;
}

session_name($app['php_session_name']);
session_start();

$userService = new UserService($dbh);

$act = $userService->getCurrentUser();

if (!$act) {
    return;
}

/**
 * User is logged in.
 */
$userService->setLastActivity();

if (!isset($_COOKIE['account'])) {
    setcookie('account', '1', ($_SERVER['REQUEST_TIME'] + 31536000), $app['cookie_path'], $app['cookie_domain'], false, false);
}
