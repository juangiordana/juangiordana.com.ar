<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

define('MODULE_PATH', __DIR__);
include __DIR__ . '/includes/database.php';
include __DIR__ . '/includes/functions.php';

$category = blogGenCategory();
$archive = blogGenArchive();
$searchHistory = blogGenSearchHistory();

if ($depth == 1) {
    /**
     * /blog/
     */
    $query = <<< 'EOD'
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
    $stmt = $dbh->query($query);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count = 0;
        $posts = '';
        $title = $row['title'];

        do {
            $posts .= blogParsePost($row, 'short');
        } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));

        $rewrite = MODULE_PATH . '/blog.php';
        return;
    }
} elseif ($depth == 2) {
} elseif ($depth == 3) {
}

if ($depth > 1) {
    /**
     * /blog/1981/04/14/
     */
    $r['y'] = (isset($level[1]) and ctype_digit($level[1])) ? $level[1] : NULL;
    $r['m'] = (isset($level[2]) and ctype_digit($level[2])) ? $level[2] : NULL;
    $r['d'] = (isset($level[3]) and ctype_digit($level[3])) ? $level[3] : NULL;

    if (isset($r['y'], $r['m'], $r['d']) and checkdate($r['m'], $r['d'], $r['y'])) {
        $mode['archive'] = 'day';
        $date['from'] = mktime(0, 0, 0, $r['m'], $r['d'], $r['y']);
        $date['to'] = mktime(23, 59, 59, $r['m'], $r['d'], $r['y']);
        $date['show'] = ucfirst(strftime('%A %e de %B de %Y', $date['from']));
    } elseif (isset($r['y'], $r['m']) and checkdate($r['m'], 1, $r['y'])) {
        $mode['archive'] = 'month';
        $date['from'] = mktime(0, 0, 0, $r['m'], 1, $r['y']);
        $date['to'] = mktime(23, 59, 59, $r['m'], idate('t', $date['from']), $r['y']);
        $date['show'] = ucfirst(strftime('%B de %Y', $date['from']));
    } elseif (isset($r['y']) and checkdate(1, 1, $r['y'])) {
        $mode['archive'] = 'year';
        $date['from'] = mktime(0, 0, 0, 1, 1, $r['y']);
        $date['to'] = mktime(23, 59, 59, 12, 31, $r['y']);
        $date['show'] = strftime('%Y', $date['from']);
    }
}

if ($depth == 2) {
    /**
     * /blog/categories/
     * /blog/authors/
     */
    if ($level[1] == 'authors') {
        $query = "SELECT `username`, CONCAT(`firstname`, ' ', `lastname`) AS `fullname` FROM `users`";
        $stmt = $dbh->query($query);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $title = 'Autores';
            $posts = "<h1>Autores</h1>\n"
                   . '<ul>';
            do {
                $posts .= '<li><a href="/blog/authors/' . $row['username'] . '/">' . $row['fullname'] . "</a></li>\n";
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            $posts .= "</ul>\n";

            $rewrite = MODULE_PATH . '/blog.php';
            return;
        }
    } elseif ($level[1] == 'categories') {
        $query  = 'SELECT `uri`, `name`, `description` FROM `categories` ORDER BY `name` ASC';
        $stmt = $dbh->query($query);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $title = 'Categorías';
            $posts  = "<h1>Categorías</h1>\n";
            $posts .= '<dl>';
            do {
                $posts .= '<dt><a href="' . $app['url'] . 'blog/categories/' . $row['uri'] . '/">' . $row['name'] . "</a></dt>\n";
                $posts .= '<dd>' . $row['description'] . "</dd>\n";
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            $posts .= "</dl>\n";

            $rewrite = MODULE_PATH . '/blog.php';
            return;
        }
    } elseif ($level[1] == 'search') {
        $rewrite = MODULE_PATH . '/search.php';
        return;
    }
} elseif ($depth == 3) {
    if ($level[1] == 'authors') {
        $query = <<< 'EOD'
SELECT
    `id`,
    `username`,
    `signindate`,
    `lastlogin`,
    `lastactivity`,
    `ipaddress`,
    `firstname`,
    `lastname`,
    CONCAT(`firstname`, ' ', `lastname`) AS `fullname`,
    `email`,
    `url`,
    `description`,
    `descriptionxhtml`,
    `signature`
FROM
    `users`
WHERE
    `username` = ?
LIMIT 1
EOD;
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($level[2]));
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $author = $row;
            $rewrite = MODULE_PATH . '/authors.php';
            return;
        }
    } elseif ($level[1] == 'categories') {
        $query = <<< 'EOD'
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
    AND
    `categories`.`uri` = ?
INNER JOIN
    `users`
ON
    `users`.`id` = `posts`.`userid`
WHERE
    `posts`.`display` = 1
ORDER BY
    `posts`.`submitiondate` DESC
EOD;
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($level[2]));
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $title = 'Categoría ' . $row['category'];
            $posts = '<h1>Categoría ' . $row['category'] . "</h1>\n"
                   . '<p>' . $stmt->rowCount() . ' publicaciones en ' . $row['category'] . "</p>\n"
                   . "<ol>\n";
            do {
                $posts .= "<li>\n" . blogParsePost($row, 'info') . "</li>\n";
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            $posts .= "</ol>\n";

            $rewrite = MODULE_PATH . '/blog.php';
            return;
        }
    } elseif ($level[1] == 'feeds') {
        if ($level[2] == 'rss') {
            $rewrite = MODULE_PATH . '/rss2.0.php';
            return;
        } elseif ($level[2] == 'atom') {
            $rewrite = MODULE_PATH . '/atom1.0.php';
            return;
        }
    }
}

if (
    $depth == 5
    and
    isset($r['y'], $r['m'], $r['d'], $level[4])
    and
    checkdate($r['m'], $r['d'], $r['y'])
) {
    $query = <<< 'EOD'
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
    `posts`.`submitiondate` BETWEEN ? AND ?
    AND
    `posts`.`uri` = ?
    AND
    `posts`.`display` = 1
LIMIT 1
EOD;
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($date['from'], $date['to'],  $level[4]));
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $posts = blogParsePost($row);

        $p = $row;

        $rewrite = MODULE_PATH . '/post.php';
        return;
    }
} else {
    if (isset($mode['archive'])) {
        $query = <<< 'EOD'
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
    `posts`.`submitiondate` BETWEEN ? AND ?
    AND
    `posts`.`display` = 1
ORDER BY
    `posts`.`submitiondate` DESC
EOD;
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($date['from'], $date['to']));
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $title = 'Archivo ' . $date['show'];
            $posts  = '<h1>Archivo ' . $date['show'] . "</h1>\n"
                    . '<p>' . $stmt->rowCount() . ' publicaciones en ' . $date['show'] . "</p>\n"
                    . "<ol>\n";
            do {
                    $posts .= "<li>\n" . blogParsePost($row, 'info') . "</li>\n";
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            $posts .= "</ol>\n";

            $rewrite = MODULE_PATH . '/blog.php';
            return;
        }
    }
}

/*
 * /blog/authors/
 *
if ($depth == 7 and isset($r['y'], $r['m'], $r['d'], $level[4]) and checkdate($r['m'], $r['d'], $r['y'])) {
        if ($level[5] == 'rss' and $level[6] == 'rss2.0') {
            $rewrite = MODULE_PATH . '/rss2.0-comments.php';
            return;
    }
}
 */
