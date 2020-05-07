#! /usr/bin/env php
<?php
require __DIR__ . '/../config.php';

ini_set('error_reporting', '-1');
ini_set('display_errors', 'stderr');
ini_set('log_errors', '0');

/**
 * Update CSS cache buster.
 */
$buster = '?' . date('Ymd');

$query = "UPDATE `settings` SET `%s` = '%s' WHERE `name` IN('css_cache_buster', 'js_cache_buster') LIMIT 2";
$query = sprintf($query, ( $app['production'] ? 'value_production' : 'value_development' ), $buster);
$stmt = $dbh->query($query);

echo "Cache buster set to '$buster'.\n";
