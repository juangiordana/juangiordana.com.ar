<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

require APP_PATH . '/include/tuples/blog.php';

$posts = blogPostInfo(array( isset($_REQUEST['id']) ? $_REQUEST['id'] : FALSE ));
$post = $posts[0];

$comments = blogPostComments($post['id']);

if (!$post) {
    header ('Location: /.admin/');
    exit;
}


$req = array(
    'category' => $post['categoryid'],
    'contents' => $post['contents'],
    'title' => $post['title'],
    'uri' => $post['uri']
);

$opt = array(
    'modificationdate' => $post['modificationdate'],
    'denycomments' => $post['denycomments'],
    'display' => $post['display'],
    'featured' => $post['featured'],
    'html' => $post['xhtml']
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
        if (isset($_POST['o'][$k])) {
            $o[$k] = validateField($_POST['o'][$k]);
        } else {
            $o[$k] = '';
        }
    }
    unset($k, $v);

    if (!isset($error)) {
        $o['modificationdate'] = ( !empty($o['modificationdate']) ? $_SERVER['REQUEST_TIME'] : $post['modificationdate'] );

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
UPDATE
    `posts`
SET
    `userid` = ?,
    `categoryid` = ?,
    `modificationdate` = ?,
    `uri` = ?,
    `title` = ?,
    `contents` = ?,
    `xhtml` = ?,
    `display` = ?,
    `featured` = ?,
    `denycomments` = ?
WHERE
    `id` = ?
LIMIT 1
EOD;
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(
            $_SESSION['admin']['id'],
            $r['category'],
            $o['modificationdate'],
            $r['uri'],
            $r['title'],
            $r['contents'],
            $o['html'],
            $o['display'],
            $o['featured'],
            $o['denycomments'],
            $post['id']
        ));

        header('Location: /.admin/blog-post-edit/?id=' . $post['id'] . '&ok=1');
        exit;
    }
} else {
    $_POST = $req + $opt;
}

$template = $twig->loadTemplate('administrator/administrator-blog-posts-add.twig');

$templateVars = array(
    'categories' => $categories,
    'comments' => $comments,
    'error' => ( isset($error) ? $error : array() ),
    'post' => $post
);
