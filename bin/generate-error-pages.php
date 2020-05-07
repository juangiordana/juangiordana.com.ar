#! /usr/bin/env php
<?php
require __DIR__ . '/../config.php';

ini_set('error_reporting', '-1');
ini_set('display_errors', 'stderr');
ini_set('log_errors', '0');

$pages = [ '403', '404', '410', '413', '503', '50x' ];

if (isset($argv[1]) && in_array($argv[1], $pages)) {
    $pages = [$argv[1]];
}

foreach ($pages as $v) {
    if ($stream = fopen($app['url'] . $v . '/', 'r')) {
        file_put_contents(
            $app['path'] . '/www/' . $v . '.html',
            $stream
        );

        fclose($stream);
    }
}
