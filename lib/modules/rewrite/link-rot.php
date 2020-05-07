<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

/**
 * Avoid Link Rot:
 */
$http301 = [
    '/blog/feeds/rss2.0/' => $app['url'] . 'blog/feeds/rss/',
    '/curriculum-vitae' => $app['url'] . 'cv/',
    '/feeds' => $app['url'],
    '/feeds/rss2.0.php' => $app['url'] . 'blog/feeds/rss/',
    '/index.php' => $app['url'],
    '/search.php' => $app['url'] . 'blog/search/',
];

if (isset($http301[$_SERVER['REQUEST_URI']])) {
    header('Location: ' . $http301[$_SERVER['REQUEST_URI']], true, 301);
    exit;
}

$http410 = [
    '/photos/',
];

if (in_array($_SERVER['REQUEST_URI'], $http410)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 410 Gone', true, 410);
    require APP_PATH . '/www/410.html';
    exit;
}

/**
 * Avoid link-rot 09/2007.
 */
if (!empty($_GET)) {
    if (preg_match('/archive.php/', $level[0], $a)) {
        if (isset($_GET['m'], $_GET['y'])) {
            if ($_GET['m'] > 0 && $_GET['m'] < 10) {
                $m = '0' . $_GET['m'];
            } elseif ($_GET['m'] >= 10 && $_GET['m'] <= 12) {
                $m = $_GET['m'];
            }

            if ($_GET['y'] >= 2004 && $_GET['y'] <= 2007) {
                $y = $_GET['y'];
            }

            if (isset($y, $m)) {
                $linkrot = "/blog/$y/$m/";
            }
        } elseif (isset($_GET['y']) && $_GET['y'] >= 2004 && $_GET['y'] <= 2007) {
            $linkrot = '/blog/' . $_GET['y'] . '/';
        }
    } elseif (preg_match('/(categories|profile|viewpost)\.php$/', $level[0], $matches)) {
        if ('categories' == $matches[1]) {
            $query = 'SELECT `uri` FROM `categories` WHERE `id` = %d LIMIT 1';
            $query = sprintf($query, $_GET['id']);
            $stmt = $dbh->query($query);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $linkrot = '/blog/categories/' . $row['uri'] . '/';
            }
        } elseif ('profile' == $matches[1]) {
            $query = 'SELECT `username` FROM `users` WHERE `id` = %d LIMIT 1';
            $query = sprintf($query, $_GET['id']);
            $stmt = $dbh->query($query);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $linkrot = '/blog/authors/' . $row['username'] . '/';
            }
        } elseif ('viewpost' == $matches[1]) {
            $query = 'SELECT `submitiondate`, `uri` FROM `posts` WHERE `id` = %d AND `display` = 1 LIMIT 1';
            $query = sprintf($query, $_GET['id']);
            $stmt = $dbh->query($query);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $linkrot = '/blog/' . date('Y/m/d', $row['submitiondate']) . '/' . $row['uri'] . '/';
            }
        }
    }
} elseif ('blog' == $level[0] && isset($level[1])) {
    if ($level[1] == 'author') {
        $linkrot = '/blog/authors/';
        if (isset($level[2])) {
            $linkrot .= ( ($level[2] == 'juangiordana') ? 'juangiordana/' : '' );
        }
    }

    if ($level[1] == 'category') {
        $linkrot = '/blog/categories/';

        if (isset($level[2])) {
            $haystack = [
                'recetas',
                'php',
                'linux',
                'opera',
                'diseno-web',
                'noticias',
                'blog',
                'articulos'
            ];

            if (in_array($level[2], $haystack)) {
                $linkrot .= $level[2] . '/';
            }
        }
    }
}

if (isset($linkrot)) {
    header('Location: ' . $linkrot, true, 301);
    exit;
}
