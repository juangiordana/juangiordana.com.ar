<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$query = 'SELECT 1 FROM `posts`';
$stmt = $dbh->query($query);
$total = $stmt->rowCount();

if ($total > 0) {
    /**
     * Available Pager options.
     */
    $sort = array('DESC', 'ASC');
    $perPage = array(20, 100, 200);

    $pagerOpts['delta'] = 5;


    $pagerOpts['sort'] = $sort[0];

    if (isset($_SESSION['admin'][__FILE__]['sort'])) {
        $pagerOpts['sort'] = $_SESSION['admin'][__FILE__]['sort'];
    }

    if (isset($_REQUEST['sort']) and in_array($_REQUEST['sort'], $sort)) {
        $pagerOpts['sort'] =
        $_SESSION['admin'][__FILE__]['sort'] = $_REQUEST['sort'];
    }


    $pagerOpts['perPage'] = $perPage[0];

    if (isset($_SESSION['admin'][__FILE__]['perPage'])) {
        $pagerOpts['perPage'] = $_SESSION['admin'][__FILE__]['perPage'];
    }

    if (isset($_REQUEST['perPage']) and in_array($_REQUEST['perPage'], $perPage)) {
        $pagerOpts['perPage'] =
        $_SESSION['admin'][__FILE__]['perPage'] = $_REQUEST['perPage'];
    }


    if (isset($_POST['pageID'])) {
        header('Location: /.admin/blog-post-list/' . (int)$_POST['pageID'] . '/');
        exit;
    }

    $pager = @ Pager::factory(array(
        'mode' => 'Jumping',
        'totalItems' => $total,
        'perPage' => $pagerOpts['perPage'],
        'delta' => $pagerOpts['delta'],
        'importQuery' => false,
        'path' => '/.admin/blog-post-list',
        'fileName' => '%d/',
        'append' => false,

        'altFirst' => 'First page',
        'altPrev' => 'Previous page',
        'altNext' => 'Next page',
        'altLast' => 'Last page %d',
        'altPage' => 'Page',

        'prevImg' => '&laquo;',
        'prevImgEmpty' => '<span>&laquo;</span>',
        'nextImg' => '&raquo;',
        'nextImgEmpty' => '<span>&raquo;</span>',

        'separator' => "\n",
        'spacesBeforeSeparator' => 0,
        'spacesAfterSeparator'  => 0,

        'linkContainer' => 'li',
        'curLinkContainerClassName' => 'active',

        'firstPagePre' => '',
        'firstPagePost' => '',
        'lastPagePre' => '',
        'lastPagePost' => '',

        'firstLinkTitle' => 'First page',
        'nextLinkTitle' => 'Next page',
        'prevLinkTitle' => 'Previous page',
        'lastLinkTitle' => 'Last page'
    ));

    $offset = $pager->getOffsetByPageId();
    $pagerLinks = $pager->getLinks();

    $query = 'SELECT `id` FROM `posts` ORDER BY `id` %s LIMIT %d, %d';
    $query = sprintf($query, $pagerOpts['sort'], ($offset[0] - 1), $pagerOpts['perPage']);

    $stmt = $dbh->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $posts = blogPostInfo($result);
}

$template = 'administrator/administrator-blog-posts-list.twig';

$templateVars = [
    'pager' => $pager,
    'pagerLinks' => $pagerLinks,
    'pagerOpts' => $pagerOpts,
    'perPage' => $perPage,
    'sort' => $sort,

    'posts' => $posts
];
