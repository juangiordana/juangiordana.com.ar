<?php
require '../.htconfig.inc.php';

header('Content-Type: text/plain');

$filename = APP_PATH . 'inc/google/sitemaps/urllist.txt';

if (!is_writable($filename)) {
    exit('The file ' . $filename . " is not writable\n");
}

if (!$handle = fopen($filename, 'w')) {
    exit('Cannot open file ' . $filename . "\n");
}
$totalLinks = 0;

// Blog - Posts
$sql = 'SELECT `id`, `submitiondate`, `modificationdate`, `uri` FROM `posts` WHERE `display` = 1 ORDER BY `id` DESC';
$result = mysql_query($sql);
if ($p = mysql_fetch_assoc($result)) {
    $totalLinks += mysql_num_rows($result);
    $content = '';
    do {
        $lastmod = $p['modificationdate'] > 0 ? $p['modificationdate'] : $p['submitiondate'];

        $sql = 'SELECT `submitiondate` FROM `comments` WHERE `postid` = ' . $p['id'] . ' ORDER BY `submitiondate` DESC LIMIT 1';
        $cresult = mysql_query($sql);
        if ($c = mysql_fetch_assoc($cresult)) {
            $lastmod = ($c['submitiondate'] > $lastmod) ? $c['submitiondate'] : $lastmod;
        }

        $content .= $app['url'] . 'blog/' . date('Y/m/d', $p['submitiondate']) . '/' . $p['uri'] . '/' .
                   ' lastmod=' . str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO', $lastmod)) .
                   ' changefreq=weekly' .
                   " priority=0.6\n";
    } while ($p = mysql_fetch_assoc($result));
}

// Blog - Categories
$sql = 'SELECT `id`, `submitiondate`, `modificationdate`, `uri` FROM `categories` ORDER BY `id` DESC';
$result = mysql_query($sql);
if ($c = mysql_fetch_assoc($result)) {
    $totalLinks += mysql_num_rows($result);

    $content .= $app['url'] . 'blog/categories/' .
                ' lastmod=' . str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO', $lastmod)) .
                ' changefreq=weekly' .
                '' .
                " priority=0.6\n";
    ++$totalLinks;

    do {
        $sql = 'SELECT `submitiondate`, `modificationdate` FROM `posts` WHERE `categoryid` = ' . $c['id'] . ' AND `display` = 1 ORDER BY `id` DESC LIMIT 1';
        $presult = mysql_query($sql);
        if ($p = mysql_fetch_assoc($presult)) {
            $lastmod = $p['modificationdate'] > 0 ? $p['modificationdate'] : $p['submitiondate'];
        } else {
            $lastmod = $c['modificationdate'] > 0 ? $c['modificationdate'] : $c['submitiondate'];
        }

        $content .= $app['url'] . 'blog/categories/' . $c['uri'] . '/' .
                   ' lastmod=' . str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO', $lastmod)) .
                   ' changefreq=weekly' .
                   " priority=0.6\n";
    } while ($c = mysql_fetch_assoc($result));
}

// Blog - Authors
$sql = 'SELECT `username`, `signindate`, `lastlogin`, `lastactivity` FROM `users` ORDER BY `lastactivity` DESC';
$result = mysql_query($sql);
if ($a = mysql_fetch_assoc($result)) {
    $totalLinks += mysql_num_rows($result);

    $lastmod = $a['lastactivity'] > 0 ? $a['lastactivity'] : $a['signindate'];

    $content .= $app['url'] . 'blog/authors/' .
             ' lastmod=' . str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO', $lastmod)) .
             ' changefreq=weekly' .
             ' priority=0.6' .
             "\n";
    ++$totalLinks;

    do {
        $lastmod = $a['lastactivity'] > 0 ? $a['lastactivity'] : $a['signindate'];

        $content .= $app['url'] . 'blog/authors/' . $a['username'] . '/' .
                   ' lastmod=' . str_replace('+0000', '+00:00', gmdate('Y-m-d\TH:i:sO', $lastmod)) .
                   ' changefreq=weekly' .
                   " priority=0.6\n";
        ++$totalLinks;
    } while ($c = mysql_fetch_assoc($result));
}

if (fwrite($handle, $content) === false) {
    exit('Cannot write to file ' . $filename . "\n");
}

fclose($handle);

echo "Dumping \$content \n";
echo $content;
echo "\n\n";

$command  = 'python ' . APP_PATH . 'inc/google/sitemaps/sitemap_gen.py';
$command .= ' --config=' . APP_PATH . 'inc/google/sitemaps/config.xml';
$command .= !PRODUCTION ? ' --testing' : '';

echo "Running $command \n";
system($command, $retval);
echo "Retval: $retval \n";
