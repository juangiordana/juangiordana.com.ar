<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

/**
 * Setup MySQL connection.
 */
if (!$app['dblink'] = @mysql_connect($db['host'], $db['user'], $db['pass'])) {
    $msg = 'Cannot connect to mysql server using: '
         . '"' . $db['host'] . '", "' . $db['user'] . '", "' . $db['pass'] . '".';
    trigger_error($msg, E_USER_ERROR);
}

$sql = 'SET autocommit = 1';
mysql_query($sql, $app['dblink']);

$sql = "SET NAMES '" . $db['char'] . "'";
mysql_query($sql, $app['dblink']);

if (!mysql_select_db($db['name'], $app['dblink'])) {
    $msg = 'Cannot select "' . $db['name'] . '" database.';
    trigger_error($msg, E_USER_ERROR);
}
