#! /usr/bin/env php
<?php
require __DIR__ . '/../config.php';

ini_set('error_reporting', '-1');
ini_set('display_errors', 'stderr');
ini_set('log_errors', '0');

$lastmod = str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO'));

echo $app['url'] , " lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=1.0\n"
   , $app['url'] , "about/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8\n"
   , $app['url'] , "blog/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8\n"
   , $app['url'] , "contact/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8\n"
   , $app['url'] , "cv/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8\n"
   , $app['url'] , "links/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8\n"
   , $app['url'] , "photos/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.8\n"
   , $app['url'] , "resume/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.6\n"
   , $app['url'] , "sitemap/ lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.6\n";

/**
 * /blog/uri/
 */
$query = 'SELECT `submitiondate`, `modificationdate`, `uri` FROM `posts` WHERE `display` = 1';
$stmt = $dbh->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $app['url'] , 'blog/' , date('Y/m/d', $row['submitiondate']) , '/' , $row['uri'] , '/ '
       , 'lastmod=' , str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO', ( ($row['modificationdate'] !== '0') ? $row['modificationdate'] : $row['submitiondate'] ))) , ' '
       , "changefreq=monthly priority=0.7\n";
}

$query = "SELECT `username` FROM `users`";
$stmt = $dbh->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $app['url'] , 'blog/authors/' , $row['username'] , '/ '
       , "lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.5\n";
}

$query = 'SELECT `uri` FROM `categories`';
$stmt = $dbh->query($query);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $app['url'] , 'blog/categories/' , $row['uri'] , '/ '
       , "lastmod=2012-09-04T18:30:00Z changefreq=monthly priority=0.5\n";
}
