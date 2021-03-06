<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$query = 'SELECT `id` FROM `posts` ORDER BY `id` DESC LIMIT 10';
$stmt = $dbh->query($query);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

$posts = blogPostInfo($result);

$comments = array();

$template = $twig->loadTemplate('administrator/administrator-dashboard.twig');

$templateVars = array(
    'comments' => $comments,
    'posts' => $posts
);
