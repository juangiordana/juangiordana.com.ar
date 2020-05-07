<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
<!DOCTYPE html>
<html class="no-js" dir="ltr" lang="es-AR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0" />
<title><?php echo $author['fullname'] . ' - ' . APP_TITLE; ?></title>
<link href="/blog/feeds/atom/" rel="alternate" title="<?php echo APP_TITLE , ' - Atom feeds'; ?>" type="application/atom+xml" />
<link href="/blog/feeds/rss/" rel="alternate" title="<?php echo APP_TITLE , ' - RSS feeds'; ?>" type="application/rss+xml" />
<?php include APP_PATH . '/include/css.php'; ?>
<?php include APP_PATH . '/include/js-head.php'; ?>
</head>
<body>
<div id="wrapper">

    <?php include APP_PATH . '/include/header.php'; ?>

    <div id="content" class="row-fluid">

        <article class="span12">

            <h1>Acerca de <?php echo $author['fullname']; ?></h1>
            <?php echo ( $author['descriptionxhtml'] ? $author['description'] : wrapString ($author['description']) ); ?>

            <h2>Datos Personales:</h2>
            <dl>
                <dt>Nombre completo:</dt>
                <dd><?php echo $author['fullname']; ?></dd>
                <dt>Fecha de registro:</dt>
                <dd><?php echo date ('d-M-Y h:j:s', $author['signindate']); ?></dd>
                <dt>Último ingreso:</dt>
                <dd><?php echo date ('d-M-Y h:j:s', $author['lastlogin']); ?></dd>
                <dt>Página web:</dt>
                <dd><?php echo $author['url'] ? '<a href="' . $author['url'] . '" rel="external">' . $author['url'] . '</a>': 'No disponible.'; ?></dd>
            </dl>

<?php
$sql = <<< 'EOD'
SELECT
    `id`,
    `submitiondate`,
    `uri`,
    `title`
FROM
    `posts`
WHERE
    `userid` = %d
    AND
    `display` = 1
ORDER BY
    `submitiondate` DESC
LIMIT 10
EOD;
$sql = sprintf($sql, $author['id']);
$result = mysql_unbuffered_query($sql);
if ($row = mysql_fetch_assoc($result)) {
?>
            <h2>Últimas publicaciones</h2>
            <ul>
<?php
    do {
?>
                <li><a href="<?php echo '/blog/' , date('Y/m/d', $row['submitiondate']) , '/' , $row['uri'] , '/'; ?>"><?php echo $row['title']; ?></a></li>
<?php
    } while ($row = mysql_fetch_assoc($result));
?>
            </ul>
<?php
}
?>

        </article>

    </div>

    <?php include APP_PATH . '/include/footer.php'; ?>

</div>
<?php include APP_PATH . '/include/js-libs.php'; ?>
<?php include APP_PATH . '/include/js-body.php'; ?>
</body>
</html>
