<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$req = [
    'password' => '',
    'password_new' => '',
    'password_confirmation' => '',
];

$opt = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($r, $o, $error) = validateRequest($req, $opt);

    if (!isset($error)) {
        /*
         * Check current password,
         */
        if (!$userService::passwordVerify($_POST['password'], $act->password)) {
            $error['password'] = true;
        }
    }

    if (!isset($error)) {
        /*
         * Check that new passwords match,
         */
        $password = $userService::passwordHash($_POST['password_new']);

        if (!$userService::passwordVerify($_POST['password_confirmation'], $password)) {
            $error['password_match'] =
            $error['password_new'] =
            $error['password_confirmation'] = true;
        }
    }

    if (!isset($error)) {
        $act->setPassword($password);

        header('Location: ' . $_SERVER['REQUEST_URI'] . '?ok=1');
        exit;
    }
}

$template = '@administrator/' . basename(__FILE__, '.php') . '.twig';
