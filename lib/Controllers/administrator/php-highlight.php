<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if (isset($_FILES['code']) and is_uploaded_file($_FILES['code']['tmp_name'])) {
    $code = highlight_file($_FILES['code']['tmp_name'], true);
}

$template = '@administrator/' . basename(__FILE__, '.php') . '.twig';

$templateVars = [
    'APP_DOMAIN' => $app['domain'],
    'APP_LOCALE' => $app['locale'],
    'APP_NAME' => $app['name'],
    'APP_URL' => $app['url'],
    'CSS_CACHE_BUSTER' => $app['css_cache_buster'],
    'JS_CACHE_BUSTER' => $app['js_cache_buster'],
    'PRODUCTION' => $app['production'],

    'cookie' => $_COOKIE,
    'get' => $_GET,
    'post' => $_POST,
    'server' => $_SERVER,
    'session' => $_SESSION,

    'act' => (isset($act) ? $act : array()),
    'code' => (isset($code) ? $code : ''),
];
