<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if (isset($_SESSION['admin'])) {
    unset($_SESSION['admin']);
}

$template = 'administrator/administrator-logout.twig';
