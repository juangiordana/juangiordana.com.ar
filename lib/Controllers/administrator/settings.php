<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$query = 'SELECT `name`, `%s` AS `value`, `type`, `title`, `description`, `edit` FROM `settings` WHERE `edit` = 1';
$query = sprintf($query, ( PRODUCTION ? 'value_production' : 'value_development' ));
$stmt = $dbh->query($query);
$settings = $stmt->fetchAll();

$req = [
    'settings' => ( isset($_SESSION['admin']['settings']) ? $_SESSION['admin']['settings'] : [] ),
];

$opt = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($r, $o, $error) = validateRequest($req, $opt);

    if (isset($_SESSION['admin']['settings'])) {
        unset($_SESSION['admin']['settings']);
    }

    if (!isset($error)) {
        $r['settings'] = array_intersect(
            array_column($settings, 'name'),
            $r['settings']
        );

        if (empty($r['settings'])) {
            $error['settings'] = 1;
        }
    }

    if (!isset($error)) {
        $_SESSION['admin']['settings'] = array_values($r['settings']);

        header('Location: /.admin/settings-edit/');
        exit;
    }
} else {
    $_POST = $req + $opt;
}

$template = '@administrator/' . basename(__FILE__, '.php') . '.twig';

$templateVars = [
    'settings' => $settings,
];
