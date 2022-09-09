#! /usr/bin/env php
<?php
require __DIR__ . '/../config.php';

ini_set('error_reporting', '-1');
ini_set('display_errors', 'stderr');
ini_set('log_errors', '0');

$lastmod = str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO'));

echo $app['url'], " lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=1.0", PHP_EOL,
    $app['url'], "about/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8", PHP_EOL,
    $app['url'], "blog/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8", PHP_EOL,
    $app['url'], "contact/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8", PHP_EOL,
    $app['url'], "cv/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8", PHP_EOL,
    $app['url'], "links/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8", PHP_EOL,
    $app['url'], "photos/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8", PHP_EOL,
    $app['url'], "resume/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.6", PHP_EOL,
    $app['url'], "sitemap/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.6", PHP_EOL;

/**
 * /blog/uri/
 */
$query = 'SELECT `submitiondate`, `modificationdate`, `uri` FROM `posts` WHERE `display` = 1';
$stmt = $dbh->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lastmod = $row['modificationdate'] !== '0' ? $row['modificationdate'] : $row['submitiondate'];
    $lastmod = gmdate('Y-m-d\TH:i:sO', $lastmod);
    $lastmod = str_replace('+0000', '+00:00', $lastmod);

    echo $app['url'], 'blog/', date('Y/m/d', $row['submitiondate']), '/', $row['uri'], '/ ',
        'lastmod=', $lastmod, ' ',
        "changefreq=monthly priority=0.7", PHP_EOL;
}

$query = "SELECT `username` FROM `users`";
$stmt = $dbh->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $app['url'], 'blog/authors/', $row['username'], '/ ',
        "lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.5", PHP_EOL;
}

$query = 'SELECT `uri` FROM `categories`';
$stmt = $dbh->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $app['url'], 'blog/categories/', $row['uri'], '/ ',
        "lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.5", PHP_EOL;
}
