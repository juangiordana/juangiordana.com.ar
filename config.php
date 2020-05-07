<?php
/**
 * Automatically load required libraries.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Loads environment variables.
 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['APP_NAME', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHAR']);

/**
 * Define main constants.
 */
define('APP_NAME', getenv('APP_NAME'));
define('APP_PATH', __DIR__);
define('PRODUCTION', in_array(getenv('APP_ENV'), ['prod', 'production']));

/**
 * Setup database connection.
 */
require_once APP_PATH . '/include/database.php';

/**
 * Load settings.
 */
$query = 'SELECT `name`, `%s` AS `value`, `type` FROM `settings` WHERE `enable` = 1';
$query = sprintf($query, (PRODUCTION ? 'value_production' : 'value_development'));
$stmt = $dbh->query($query);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $app = [
        'name' => APP_NAME,
        'path' => APP_PATH,
        'production' => PRODUCTION,
    ];

    do {
        settype($row['value'], $row['type']);

        define(strtoupper($row['name']), $row['value']);
        $app[$row['name']] = $row['value'];
    } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
} else {
    $msg = 'Cannot load settings... Exiting.';
    trigger_error($msg, E_USER_ERROR);
}

/**
 * Locale settings.
 */
require_once APP_PATH . '/include/l10n.php';

/**
 * Load application functions.
 */
require_once APP_PATH . '/include/functions/functions.php';

/**
 * Offline manager:
 * - Grant access from the command line.
 * - Grant access by IP address.
 * - Grant access to site administrator bypassing the IP restriction.
 */
if ($app['offline']) {
    if (PHP_SAPI != 'cli'
        && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $app['offline_allow']))
        && substr($_SERVER['REQUEST_URI'], 0, 8) != '/.admin/'
    ) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 10');

        require APP_PATH . '/www/503.html';
        exit;
    }
}

/**
 * Sessions.
 */
require_once APP_PATH . '/include/session.php';
