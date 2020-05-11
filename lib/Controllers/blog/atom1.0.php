<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$sql = 'SELECT `submitiondate` FROM `posts` WHERE `display` = 1 ORDER BY `submitiondate` DESC LIMIT 1';
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);

header('Content-type: application/atom+xml');

echo '<?xml version="1.0" encoding="utf-8"?>'
   , '<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="es">'
   , '<id>' , $app['url'] , 'blog/feeds/atom1.0/</id>'
   , '<title>Juan Giordana</title>'
   , '<link href="' , $app['url'] , 'blog/feeds/atom1.0/" rel="self" type="application/atom+xml" />'
   , '<link href="' , $app['url'] , 'blog/" rel="alternate" type="text/html" />'
   , '<updated>' . date('c', $row['submitiondate']) . '</updated>'
   , '<subtitle>Juan Francisco Giordana Atom Feeds</subtitle>'
   , '<generator>JFG Feed Generator v0.1</generator>';

$sql = <<< 'EOD'
SELECT
    `posts`.`id`,
    `posts`.`userid`,
    `posts`.`categoryid`,
    `posts`.`submitiondate`,
    `posts`.`modificationdate`,
    `posts`.`uri`,
    `posts`.`title`,
    `posts`.`contents`,
    `posts`.`xhtml`,
    `posts`.`comments`,
    `posts`.`denycomments`,
    `users`.`username`,
    CONCAT(`users`.`firstname`, ' ', `users`.`lastname`) AS `author`,
    `users`.`url` AS `user_url`,
    `categories`.`name` AS `category`,
    `categories`.`uri` AS `category_uri`
FROM
    `posts`
INNER JOIN
    `categories`
ON
    `categories`.`id` = `posts`.`categoryid`
INNER JOIN
    `users`
ON
    `users`.`id` = `posts`.`userid`
WHERE
    `posts`.`display` = 1
ORDER BY
    `posts`.`submitiondate` DESC
LIMIT 7
EOD;
$result = mysql_unbuffered_query($sql);
while ($row = mysql_fetch_assoc($result)) {
    /**
     * Tag          Description
     * <author>     Defines the author of the item. --> must be an email address.
     * <category>   Places the item in one or more of the channel categories.
     * <enclosure>  Describes a media object related to the item
     * <guid>       GUID = Globally Unique Identifier. Defines a unique identifier to the item.
     * <pubDate>    The publication date for the item.
     * <source>     Is used to define a third party source.
     */
    $uri = $app['url'] . 'blog/' . date('Y/m/d', $row['submitiondate']) . '/' . $row['uri'] . '/';

    echo '<entry>'
       , '  <id>' , $uri , '</id>'
       , '  <title>' , $row['title'] , '</title>'
       , '  <link href="' , $uri , '" rel="alternate" type="text/html" />'
       , '  <published>' , date('c', $row['submitiondate']) , '</published>'
       , ( ($row['modificationdate'] != '') ? '<updated>' . date('c', $row['modificationdate']) . '</updated>' : '' )
       , '  <author>'
       , '    <name>' , $row['author'] , '</name>'
       , '    <uri>' , $row['user_url'] , '</uri>'
       , '  </author>'
       , '  <summary type="xhtml">'
       , '    <div xmlns="http://www.w3.org/1999/xhtml">' , $row['contents'] , '</div>'
       , '  </summary>'
       , '</entry>';
}

echo '</feed>';
