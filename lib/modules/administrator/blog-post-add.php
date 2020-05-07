<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

require APP_PATH . '/include/tuples/blog.php';

$req = array(
    'category' => $categories[0],
    'contents' => '',
    'title' => '',
    'uri' => ''
);

$opt = array(
    'denycomments' => '',
    'display' => '',
    'featured' => '',
    'html' => ''
);

if (!empty($_POST)) {
    /**
     * Validate required fields.
     */
    foreach ($req as $k => &$v) {
        if (!isset($_POST[$k])) {
            $error[$k] = 1;
        } else {
            $r[$k] = validateField($_POST[$k]);
            if ($r[$k] == '') {
                $error[$k] = 1;
            }
        }
    }
    unset($k, $v);

    /**
     * Validate optional fields.
     */
    foreach ($opt as $k => &$v) {
        if (isset($_POST[$k])) {
            $o[$k] = validateField($_POST[$k]);
        } else {
            $o[$k] = '';
        }
    }
    unset($k, $v);

    if (!isset($error)) {
        $o['html'] = ( empty($o['html']) ? 0 : 1 );
        $o['display'] = ( empty($o['display']) ? 0 : 1 );
        $o['featured'] = ( empty($o['featured']) ? 0 : 1 );
        $o['denycomments'] = ( empty($o['denycomments']) ? 0 : 1 );

        $r['contents'] = validateField($_POST['contents'], ($o['html'] == 0), TRUE);

        if (empty($r['contents'])) {
            $error['contents'] = 1;
        }
    }

    if (!isset($error)) {
        $query = <<< 'EOD'
INSERT INTO
    `posts`
SET
    `userid` = ?,
    `categoryid` = ?,
    `submitiondate` = ?,
    `uri` = ?,
    `title` = ?,
    `contents` = ?,
    `xhtml` = ?,
    `display` = ?,
    `featured` = ?,
    `denycomments` = ?
EOD;
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(
            $_SESSION['admin']['id'],
            $r['category'],
            $_SERVER['REQUEST_TIME'],
            $r['uri'],
            $r['title'],
            $r['contents'],
            $o['html'],
            $o['display'],
            $o['featured'],
            $o['denycomments']
        ));

        header('Location: /.admin/blog-post-edit/?id=' . $dbh->lastInsertId());
        exit;
    }
} else {
    $_POST = $req + $opt;
}

$template = $twig->loadTemplate('administrator/administrator-blog-posts-add.twig');

$templateVars = array(
    'categories' => $categories,
    'error' => ( isset($error) ? $error : array() )
);
