<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if ($act) {
    header('Location: /');
    exit;
}

$req = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',

    'username' => '',
    'password' => '',
    'password_confirmation' => '',
];

$opt = [
    'phone' => '',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($r, $o, $error) = validateRequest($req, $opt);

    if (!isset($error)) {
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $error['password'] =
            $error['password_confirmation'] = 1;
        }
    }

    if (!isset($error)) {
        $r['first_name'] = ucfirst(strtolower($r['first_name']));
        $r['last_name'] = ucfirst(strtolower($r['last_name']));

        $r['username'] = strtolower($r['username']);

        if (preg_match('/[^0-9a-z_\-\.]/i', $r['username'])) {
            $error['username_invalid'] = 1;
        }
    }

    if (!isset($error)) {
        $userService->register();

        $userService->login();

        header('Location: /account/');
        exit;
    }
}

$template = $app['template'] . '/modules/' . basename(__DIR__). '/' . basename(__FILE__, '.php') . '.twig';
