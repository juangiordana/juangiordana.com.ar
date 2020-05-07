<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if ($act) {
    header('Location: /');
    exit;
}

$req = [
    'username' => '',
    'password' => '',
];

$opt = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($r, $o, $error) = validateRequest($req, $opt);

    if (!isset($error)) {
        $auth = $userService->authenticate($r['username'], $_POST['password']);

        if (!$auth) {
            $error['username'] =
            $error['password'] = 1;
        }
    }

    if (!isset($error)) {
        $userService->login();

        /**
         * Restore REQUEST_URI from previous access.
         */
        if (isset($_SESSION['login_redirect'])) {
            $location = $_SESSION['login_redirect'];
            unset($_SESSION['login_redirect']);
        } else {
            $location = '/';
        }

        header('Location: ' . $location);
        exit;
    }
}

$template = $twig->loadTemplate($app['template'] . '/modules/' . basename(__DIR__). '/' . basename(__FILE__, '.php') . '.twig');
