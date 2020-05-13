<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if (empty($_SESSION['admin']['settings'])) {
    header('Location: /.admin/settings/');
    exit;
}

$params = [];
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


$req = $opt = [];

foreach ($settings as $k => &$v) {
    $req['setting_name_' . $k] = $v['name'];
    $req['setting_type_' . $k] = $v['type'];
    $req['setting_title_' . $k] = $v['title'];
    $opt['setting_value_' . $k] = $v['value'];
    $opt['setting_description_' . $k] = $v['description'];
}
unset($k, $v);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($r, $o, $error) = validateRequest($req, $opt);

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
            $stmt->execute([
                $r['setting_name_' . $k],
                $o['setting_value_' . $k],
                $r['setting_type_' . $k],
                $r['setting_title_' . $k],
                $o['setting_description_' . $k],
                $v['name'],
            ]);
        }
        unset($k, $v);

        unset($_SESSION['admin']['settings']);

        header('Location: /.admin/settings/?ok=1');
        exit;
    }
} else {
    $_POST = $req + $opt;
}

$template = '@administrator/' . basename(__FILE__, '.php') . '.twig';

$templateVars = [
    'settings' => $settings,
];
