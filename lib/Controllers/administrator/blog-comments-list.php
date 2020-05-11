<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$sql = 'SELECT id FROM comments ORDER BY id DESC';
$result = mysql_unbuffered_query($sql);
if ($row = mysql_fetch_assoc($result)) {
    $params = array(
        'perPage' => 50,
        'delta' => 10,
        'separator' => ' ',
        'spacesBeforeSeparator' => 0,
        'spacesAfterSeparator' => 0,
        'mode' => 'Jumping',
        'urlVar' => 'p',
        'linkClass' => 'pager',
        'curPageLinkClassName' => 'pagerCur'
    );
    do {
        $params['itemData'][] = $row['id'];
    } while ($row = mysql_fetch_assoc($result));
    $pager = @ Pager::factory($params);
    $links = $pager->getLinks();
    $results  = $pager->getPageData();
    unset($params);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title><?php echo ADMIN_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="/css/admin.css" />
</head>
<body>
<div id="wrapper">
  <?php include __DIR__ . '/header.inc.php'; ?>
  <div id="content">
    <h2>Posts List</h2>
<?php
if (isset($results)) {
?>
      <table class="tbl">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Submited</th>
            <th class="short">Display</th>
            <th>Comments</th>
            <th class="short">Edit</th>
          </tr>
        </thead>
        <tbody class="c">
<?php
    $bgcolor = 0;
    foreach ($results as $id) {
        $sql = 'SELECT id, title, submitiondate, display, comments FROM posts WHERE id = ' . $id . ' LIMIT 1';
        $result = mysql_query($sql);
        if ($row = mysql_fetch_assoc($result)) {
            $rowatt = ++$bgcolor % 2 == 0 ? ' class="bg"' : '';
?>
          <tr <?php echo $rowatt; ?>>
            <td><?php echo $id; ?></td>
            <td class="l"><?php echo $row['title']; ?></td>
            <td><?php echo date('Y-M-d', $row['submitiondate']); ?></td>
            <td class="short"><?php echo $row['display'] == 1 ? '<span class="success">Y</span>' : '<span class="error">N</span>'; ?></td>
            <td class="short"><?php echo $row['comments']; ?></td>
            <td class="short"><a href="/.admin/blog-post-edit/?id=<?php echo $id; ?>">Edit</a></td>
          </tr>
<?php
        }
    }
?>
          </tbody>
        </table>
<?php
    if (!empty ($links['pages'])) {
        echo '<ul class="pages">';
        echo '<li>' . $links['back'] . '</li>';
        echo '<li>' . $links['pages'] . '</li>';
        echo '<li>' . $links['next'] . '</li>';
        echo '</ul>';
    }
} else {
?>
    <p>- No posts loaded - </p>
<?php
}
?>
  </div>
<?php
include __DIR__ . '/navbar.inc.php';
include __DIR__ . '/footer.inc.php';
?>
</div>
</body>
</html>
