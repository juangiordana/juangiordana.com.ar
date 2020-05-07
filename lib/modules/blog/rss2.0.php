<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$sql = 'SELECT `submitiondate` FROM `posts` WHERE `display` = 1 ORDER BY `submitiondate` DESC LIMIT 1';
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);

header('Content-type: application/rss+xml');

echo '<?xml version="1.0" encoding="utf-8"?>'
   , '<rss version="2.0">'
   , '  <channel>'
   , '    <title>Juan Francisco Giordana</title>'
   , '    <link>', $app['url'] , '</link>'
   , '    <description>Juan Francisco Giordana RSS Feeds</description>'
   , '    <language>es</language>'
   , '    <pubDate>' , date('r', $row['submitiondate']) , '</pubDate>'
   , '    <lastBuildDate>' , date('r', $row['submitiondate']) , '</lastBuildDate>'
   , '    <docs>http://blogs.law.harvard.edu/tech/rss</docs>'
   , '    <generator>JFG Feed Generator v0.1</generator>'
   , '    <managingEditor>' , $app['email_contact'] , '</managingEditor>'
   , '    <webMaster>' , $app['email_contact'] , '</webMaster>';

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
     * <comments>   An URL to a comment's page for the item.
     * <enclosure>  Describes a media object related to the item
     * <guid>       GUID = Globally Unique Identifier. Defines a unique identifier to the item.
     * <pubDate>    The publication date for the item.
     * <source>     Is used to define a third party source.
     */
    $uri = $app['url'] . 'blog/' . date('Y/m/d', $row['submitiondate']) . '/' . $row['uri'] . '/';

    echo '    <item>'
       , '      <title>' , $row['title'] , '</title>'
       , '      <category>' , $row['category'] , '</category>'
       , '      <link>' , $uri , '</link>'
       , '      <pubDate>' , date('r', (($row['modificationdate'] == '') ? $row['submitiondate'] : $row['modificationdate'] )) , '</pubDate>'
       , '      <guid>' , $uri , '</guid>'
       , '      <description><![CDATA[', $row['contents'] , ']]></description>'
       , '      <comments>' , $uri , '#comments</comments>'
       , '    </item>';
}

echo '  </channel>'
   , '</rss>';
