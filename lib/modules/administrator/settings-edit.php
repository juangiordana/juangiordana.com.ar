<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if (empty($_SESSION['admin']['settings'])) {
    header('Location: /.admin/settings/');
    exit;
}

$params = array();
$place_holders = '';

$params = array_values($_SESSION['admin']['settings']);
$place_holders = implode(',', array_fill(0, count($params), '?'));

$query = <<< 'EOD'
SELECT
    `name`,
    `%s` AS `value`,
    `type`,
    `title`,
    `description`,
    `edit`
FROM
    `settings`
WHERE
    `name` IN (%s)
EOD;
$query = sprintf($query, ( PRODUCTION ? 'value_production' : 'value_development' ), $place_holders);
$stmt = $dbh->prepare($query);
$stmt->execute($params);
$settings = $stmt->fetchAll();


$req =
$opt = array();

foreach ($settings as $k => &$v) {
    $req['setting_name_' . $k] = $v['name'];
    $req['setting_type_' . $k] = $v['type'];
    $req['setting_title_' . $k] = $v['title'];
    $opt['setting_value_' . $k] = $v['value'];
    $opt['setting_description_' . $k] = $v['description'];
}
unset($k, $v);

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

    if (!isset($error)) {
        foreach ($settings as $k => &$v) {
            $query = <<< 'EOD'
UPDATE
    `settings`
SET
    `name` = ?,
    `%s` = ?,
    `type` = ?,
    `title` = ?,
    `description` = ?
WHERE
    `name` = ?
LIMIT 1
EOD;
            $query = sprintf($query, ( PRODUCTION ? 'value_production' : 'value_development' ));
            $stmt = $dbh->prepare($query);
            $stmt->execute(array(
                $r['setting_name_' . $k],
                $o['setting_value_' . $k],
                $r['setting_type_' . $k],
                $r['setting_title_' . $k],
                $o['setting_description_' . $k],
                $v['name']
            ));
        }
        unset($k, $v);

        unset($_SESSION['admin']['settings']);

        header('Location: /.admin/settings/?ok=1');
        exit;
    }
} else {
    $_POST = $req + $opt;
}

$template = $twig->loadTemplate('administrator/administrator-settings-edit.twig');

$templateVars = array(
    'settings' => $settings
);
