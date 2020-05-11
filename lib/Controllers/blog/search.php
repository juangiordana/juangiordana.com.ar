<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if (!empty($_GET) and $search['q'] = validateField($_GET['q'], FALSE)) {
    if (is_numeric($search['q'])) {
        $sql = "SELECT `id` FROM `posts` WHERE `id` = %d AND `display` = 1 LIMIT 1";
    } else {
        $sql = "SELECT `id` FROM `posts` WHERE `title` LIKE '%1\$s' OR `contents` LIKE '%1\$s' AND `display` = 1";
    }
    $sql = sprintf($sql, $search['q']);
    $result = mysql_unbuffered_query($sql);
    if ($s = mysql_fetch_assoc($result)) {
        do {
            $search['results'][] = $s['id'];
        } while ($s = mysql_fetch_assoc($result));
    } else {
        /**
         * If no matches were found, try against the title.
         */
        $sql = "SELECT `id` FROM `posts` WHERE `title` LIKE '%%%s%%' AND `display` = 1";
        $sql = sprintf($sql, $search['q']);
        $result = mysql_unbuffered_query($sql);
        if ($s = mysql_fetch_assoc($result)) {
            do {
                $search['results'][] = $s['id'];
            } while ($s = mysql_fetch_assoc($result));
        }
    }
}

if (isset($search['results'])) {
    $sql = "SELECT `id`, `rankupdate` FROM `search_history` WHERE `query` = '%s' LIMIT 1";
    $sql = sprintf($sql, $search['q']);
    $result = mysql_query($sql);
    if ($s = mysql_fetch_assoc($result)) {
        if (time() - $s['rankupdate'] > 60) {
            $sql = 'UPDATE `search_history` SET `rank` = (`rank` + 1), `rankupdate` = UNIX_TIMESTAMP() WHERE `id` = %d LIMIT 1';
            $sql = sprintf($sql, $s['id']);
            mysql_query($sql);
        }
    } else {
        $sql = "INSERT INTO `search_history` SET `query` = '%s', `rankupdate` = UNIX_TIMESTAMP(), `display` = 1";
        $sql = sprintf($sql, $search['q']);
        mysql_query($sql);
    }
}
?>
<!DOCTYPE html>
<html class="no-js" dir="ltr" lang="es-AR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0" />
<title><?php echo ( isset($search['q']) ? htmlentities($search['q'], ENT_COMPAT, 'UTF-8') . ' - ' . APP_TITLE : APP_TITLE ); ?></title>
<?php include APP_PATH . '/include/css.php'; ?>
<?php include APP_PATH . '/include/js-head.php'; ?>
</head>
<body>
<div id="wrapper">

    <?php include APP_PATH . '/include/header.php'; ?>

    <div id="content" class="row-fluid">

        <article class="span12">
            <h2>Buscar en Blog</h2>

<?php
if (!empty($search['results'])) {
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
    `posts`.`id` IN(%s)
ORDER BY
    `posts`.`submitiondate` DESC
EOD;
    $sql = sprintf($sql, implode(',', $search['results']));
    $result = mysql_unbuffered_query($sql);
    if ($row = mysql_fetch_assoc($result)) {
?>
            <h3>Su búsqueda arrojó <strong><?php echo count($search['results']); ?></strong> resultados</h3>
            <ol>
<?php
        do {
?>
                <li><?php echo blogParsePost($row, 'info'); ?></li>
<?php
        } while ($row = mysql_fetch_assoc($result));
?>
            </ol>
<?php
    }
} else {
    if (!empty($_POST)) {
?>
            <p>Su búsqueda no arrojó ningún resultado.</p>
<?php
    }
}
?>

            <hr />
            <h3>Sugerencias:</h3>
            <ul>
                <li>Asegúrese que todas las palabras están escritas de manera apropiada.</li>
                <li>Inténtelo nuevamente utilizando diferentes <em>palabras clave</em>.</li>
                <li>Inténtelo nuevamente utilizando <em>palabras clave</em> más generales.</li>
            </ul>
        </article>

    </div>

    <?php include APP_PATH . '/include/footer.php'; ?>

</div>
<?php include APP_PATH . '/include/js-libs.php'; ?>
<?php include APP_PATH . '/include/js-body.php'; ?>
</body>
</html>
