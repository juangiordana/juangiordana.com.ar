<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$query = 'SELECT `id` FROM `posts` ORDER BY `id` DESC LIMIT 10';
$stmt = $dbh->query($query);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

$posts = blogPostInfo($result);

$comments = [];

$template = '@administrator/' . basename(__FILE__, '.php') . '.twig';

$templateVars = [
    'comments' => $comments,
    'posts' => $posts
];
