<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$db['host'] = getenv('DB_HOST');
$db['user'] = getenv('DB_NAME');
$db['pass'] = getenv('DB_USER');
$db['name'] = getenv('DB_PASS');
$db['char'] = getenv('DB_CHAR');

/**
 * Setup database connection.
 */
try {
    $dbh = new PDO(
        'mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=' . $db['char'],
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_AUTOCOMMIT => 1,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
        ]
    );
} catch (PDOException $e) {
    $msg = 'Cannot connect to MySQL server using: '
         . '"' . $db['host'] . '", "' . $db['user'] . '", "' . $db['pass']
         . '", "' . $db['name'] . '", "' . $db['char'] . '".';
    trigger_error($msg, E_USER_ERROR);
}
