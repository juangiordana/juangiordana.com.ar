<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$query = 'SELECT `name`, `%s` AS `value`, `type`, `title`, `description`, `edit` FROM `settings` WHERE `edit` = 1';
$query = sprintf($query, ( PRODUCTION ? 'value_production' : 'value_development' ));
$stmt = $dbh->query($query);
$settings = $stmt->fetchAll();

$req = array(
    'settings' => ( isset($_SESSION['admin']['settings']) ? $_SESSION['admin']['settings'] : array() ),
);

$opt = array();

if (!empty($_POST)) {
    /**
     * Validate required fields.
     */
    foreach ($req as $k => &$v) {
        if (!isset($_POST[$k])) {
            $error[$k] = 1;
        } else {
            $r[$k] = validateField($_POST[$k]);
            if ($r[$k] == '') {
                $error[$k] = 1;
            }
        }
    }
    unset($k, $v);

    /**
     * Validate optional fields.
     */
    foreach ($opt as $k => &$v) {
        if (isset($_POST[$k])) {
            $o[$k] = validateField($_POST[$k]);
        } else {
            $o[$k] = '';
        }
    }
    unset($k, $v);

    if (isset($_SESSION['admin']['settings'])) {
        unset($_SESSION['admin']['settings']);
    }

    if (!isset($error)) {
        $r['settings'] = array_intersect(
            array_column('name', $settings),
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

$template = 'administrator/administrator-settings.twig';

$templateVars = array(
    'settings' => $settings
);
