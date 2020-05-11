<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

function blogPostInfo($params = array())
{
    global $app, $dbh;

    $posts = array();

    /**
     * Create a string for the parameter placeholders filled to the number of
     * params
     */
    $count = count($params);
    $place_holders = implode(', ', array_fill(0, $count, '?'));

    $query = <<< 'EOD'
SELECT
    `id`,
    `userid`,
    `categoryid`,
    `submitiondate`,
    `modificationdate`,
    `uri`,
    `title`,
    `contents`,
    `xhtml`,
    `display`,
    `featured`,
    `comments`,
    `denycomments`
FROM
    `posts`
WHERE
    `id` IN(%s)
LIMIT %d
EOD;
    $query = sprintf($query, $place_holders, $count);
    $stmt = $dbh->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll();

    foreach ($result as &$row) {
        $post = $row;

        $post['url'] = '//' . $app['domain'] . '/blog/' . date('Y/m/d/', $row['submitiondate']) . $row['uri'] . '/';
        $post['url_edit'] = '//' . $app['domain'] . '/.admin/blog-post-edit/?id=' . $row['id'];

        /**
         * Post excerpt.
         */
        $excerpt = strpos($row['contents'], '<!-- more -->');

        if ($excerpt !== false) {
            $post['excerpt'] = substr($row['contents'], 0, $excerpt) . ' ...';
        } else {
            $post['excerpt'] = $row['contents'];
        }

        /**
         * Sort.
         */
        $key = array_search($row['id'], $params);
        $posts[$key] = $post;
        unset($key, $post);
    }
    unset($result, $row);

    ksort($posts);

    return $posts;
}

function blogPostComments($postid)
{
    global $app, $dbh;

    $comments = array();

    $query = <<< 'EOD'
SELECT
    `comments`.`id`,
    `comments`.`userid`,
    `comments`.`submitiondate`,
    `comments`.`fullname`,
    `comments`.`email`,
    `comments`.`url`,
    `comments`.`contents`,
    `posts`.`submitiondate` AS `post_submitiondate`,
    `posts`.`uri` AS `post_uri`,
    `users`.`username`
FROM
    `comments`
INNER JOIN
    `posts`
ON
    `posts`.`id` = `comments`.`postid`

LEFT JOIN
    `users`
ON
    `users`.`id` = `comments`.`userid`
WHERE
    `comments`.`postid` = ?
ORDER BY
    `comments`.`id` ASC
EOD;
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($postid));
    $result = $stmt->fetchAll();

    foreach ($result as &$row) {
        $comment = $row;

        $comment['url'] = '//' . $app['domain'] . '/blog/' . date('Y/m/d/', $row['post_submitiondate']) . $row['post_uri'] . '/#' . $row['id'];
        $comment['url_edit'] = '//' . $app['domain'] . '/.admin/blog-comment-edit/?id=' . $row['id'];


        $excerpt = ( (strlen($row['contents']) > 100) ? 100 : false );

        if ($excerpt !== false) {
            $comment['excerpt'] = substr($row['contents'], 0, $excerpt) . ' ...';
        } else {
            $comment['excerpt'] = $row['contents'];
        }

        $comments[] = $comment;
    }
    unset($result, $row);

    return $comments;
}

function blogParsePost($p, $mode = false)
{
    global $app;

    $uri = '/blog/' . date('Y/m/d', $p['submitiondate']) . '/' . $p['uri'] . '/';
    $url = $app['url'] . 'blog/' . date('Y/m/d', $p['submitiondate']) . '/' . $p['uri'] . '/';

    $post = '<article class="post">';

    if ($mode == 'info') {
            $post .= '  <h2><a href="' . $uri . '" title="' . $p['title'] . '">' . $p['title'] . "</a></h2>\n";
    } elseif ($mode == 'short') {
        $post .= '<header>'
              . '  <h2><a href="' . $uri . '" title="' . $p['title'] . '">' . $p['title'] . '</a></h2>'
              . '  <p>'
              . '    <time datetime="' . strftime('%FT%T', $p['submitiondate']) . '" pubdate>'
              . ucfirst(strftime('%A %d de %B de %Y a las %H:%M:%S', $p['submitiondate']))
              . '    </time>'
              . '  </p>'
              . '</header>';

        $content = ( $p['xhtml'] ? $p['contents'] : wrapString($p['contents']) );

        if ($cut = blogCutPost($content)) {
            $post .= $cut . '<p><a href="' . $uri . '" title="' . $p['title'] . '">Leer entrada completa</a></p>';
        } else {
            $post .= $content;
        }
    } else {
        $post .= '<header>'
               . '  <h1><a href="' . $uri . '" title="' . $p['title'] . '">' . $p['title'] . "</a></h1>\n"
               . '  <p>'
               . '    <time datetime="' . strftime('%FT%T', $p['submitiondate']) . '" pubdate>'
               . ucfirst(strftime('%A %d de %B de %Y a las %H:%M:%S', $p['submitiondate']))
               . '    </time>'
               . '  </p>'
               . '</header>'
               . ( $p['xhtml'] ? $p['contents'] : wrapString($p['contents']) )
               .  '  <footer>'
               .  '    <dl class="dl-horizontal">'
               .  '      <dt>Publicado:</dt>'
               .  '      <dd>'
               .  '        <time datetime="' . strftime('%FT%T', $p['submitiondate']) . '" pubdate>'
               .  ucfirst(strftime('%A %d de %B de %Y a las %H:%M:%S', $p['submitiondate']))
               .  '        </time>'
               .  '      </dd>';

        if (!empty($p['modificationdate'])) {
            $post .= '      <dt>Última modificación:</dt>'
                  .  '      <dd>'
                  .  '        <time datetime="' . strftime('%FT%T', $p['modificationdate']) . '">'
                  .  ucfirst(strftime('%A %d de %B de %Y a las %H:%M:%S', $p['modificationdate']))
                  .  '        </time>'
                  .  '      </dd>';
        }

        $post .= '      <dt>Autor:</dt>'
              .  '      <dd><a href="/blog/authors/' . $p['username'] . '/">' . $p['author'] . '</a></dd>'
              .  '      <dt>Categoría:</dt>'
              .  '      <dd><a href="/blog/categories/' . $p['category_uri'] . '/">' . $p['category'] . '</a></dd>'
              .  '      <dt>Comentarios:</dt>'
              .  '      <dd>';

        if ($p['comments'] > 0) {
            $post .= '      <a href="' . $uri . '#comments">' . ($p['comments'] == 1 ? '1 comentario' : $p['comments'] . ' comentarios') . '</a>';
        } else {
            $post .= '      <a href="' . $uri . '#commentsAdd">Sin comentarios</a>';
        }

        $post .= '      </dd>'
              .  '      <dd><a href="#commentsAdd">Agregar comentario</a></dd>';

        if (!$mode) {
            $post .= '      <dt>Comentarios <abbr title="Really Simple Syndication">RSS</abbr>:</dt>'
                  .  '      <dd><a href="' . $uri . 'feeds/rss/">Suscribirse a estos comentarios.</a></dd>'
                  .  '      <dd>Suscribirse a estos comentarios.</dd>';
        }

        $post .= '    </dl>'
              .  '  </footer>';
    }

    $post .= '</article>';

    return $post;
}

function blogCutPost($content)
{

    $position = strpos($content, '<!-- more -->');

    if ($position !== false) {
        return substr($content, 0, $position);
    }

    /**
     * Deprecated (must be removed).
     */
    $limit = 2;
    $lines = explode('</p>', $content);

    if ((count($lines) - $limit) > $limit) {
        $i = 0;
        $cut = '';
        while ($i < $limit) {
            $cut .= $lines[$i++] . "</p>\n";
        }

        return $cut;
    }

    return false;
}

function blogGenComments($postid)
{
    global $app, $dbh;

    $query = <<< 'EOD'
SELECT
    `comments`.`id`,
    `comments`.`userid`,
    `comments`.`submitiondate`,
    `comments`.`fullname`,
    `comments`.`email`,
    `comments`.`url`,
    `comments`.`contents`,
    `users`.`username`
FROM
    `comments`
LEFT JOIN
    `users`
ON
    `users`.`id` = `comments`.`userid`
WHERE
    `postid` = ?
    AND
    `display` = 1
ORDER BY
    `id` ASC
EOD;
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($postid));
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $comments = '<ol>';
        do {
            $comments .= '<li id="comment-' . $row['id'] . '" class="' . (!empty($row['userid']) ? 'author' : 'anonym') . '">'
                      .  blogParseComments($row)
                      .  "</li>\n";
        } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
        $comments .= '</ol>';
    } else {
        $comments = '<p>No se ha cargado ningún comentario.</p>';
    }

    return $comments;
}

function blogParseComments($row)
{
    $comment = wrapString($row['contents'])
             . '<p>Enviado por ';

    if (!empty($row['username'])) {
        $comment .= '<a href="/blog/authors/' . $row['username'] . '">' . $row['fullname'] . '</a>';
    } else {
        $comment .= !empty($row['url']) ? '<a href="' . $row['url'] . '">' . $row['fullname'] . '</a>' : $row['fullname'];
    }

    $comment .= ' el ' . date('d/m/Y h:j:s', $row['submitiondate'])
             .  ' (<a href="#comment-' . $row['id'] . '" title="Enlace a comentario">#</a>).</p>';

    return $comment;
}

function blogGenArchive()
{
    global $app, $dbh;

    $query = 'SELECT MIN(`submitiondate`) AS `first_post`, MAX(`submitiondate`) AS `last_post` FROM `posts` LIMIT 1';
    $stmt = $dbh->query($query);
    if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return false;
    }

    $archive = array('html', 'link');

    $firstPost['stamp'] = $row['first_post'];
    $firstPost['y'] = date('Y', $row['first_post']);
    $firstPost['m'] = date('n', $row['first_post']);

    $lastPost['stamp'] = $row['last_post'];
    $lastPost['y'] = date('Y', $row['last_post']);
    $lastPost['m'] = date('n', $row['last_post']);

    $archive['html'] = '<dl>';
    $archive['link'] = '';
    $m = $lastPost['m'];
    for ($y = $lastPost['y']; $y >= $firstPost['y']; --$y) {
        $archive['html'] .= '<dt><a href="/blog/' . $y . '/" title="Año ' . $y . '">' . $y . '</a></dt>';
        $archive['html'] .= '<dd><ul>';
        $k = $y == $firstPost['y'] ? $firstPost['m'] : 1;
        for ($m; $m >= $k; --$m) {
            $month['stamp'] = mktime(0, 0, 0, $m, 1, $y);
            $month['num'] = date('m', $month['stamp']);
            $month['text'] = ucfirst(strftime('%B', $month['stamp']));

            $title = $month['text'] . ' de ' . $y;
            $href = '/blog/' . $y . '/' . $month['num'] . '/';

            $archive['html'] .= '<li><a href="' . $href . '" title="' . $title . '">' . $month['text'] . '</a></li>';
            $archive['link'] .= '<link href="' . $href . '" rel="archives" title="' . $title . '">' . "\r\n";
        }
        unset($m);
        $m = 12;
        $archive['html'] .= '</ul>';
        $archive['html'] .= '</dd>';
    }
    $archive['html'] .= '</dl>';

    return $archive;
}

function blogGenCategory()
{
    global $app, $dbh;

    $category = array('html');
    $query = 'SELECT `uri`, `name` FROM `categories` ORDER BY `name` ASC';
    $stmt = $dbh->query($query);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category['html'] = '<ul>';
        do {
            $category['html'] .= '<li><a href="/blog/categories/' . $row['uri'] . '/">' . $row['name'] . "</a></li>\n";
        } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
        $category['html'] .= "</ul>\n";
    }

    return $category;
}

function blogGenSearchHistory()
{
    global $app, $dbh;

    $sh = array('html');
    $query  = 'SELECT `query` FROM `search_history` WHERE `display` = 1 ORDER BY `rank` DESC LIMIT 10';
    $stmt = $dbh->query($query);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sh['html'] = '<ol>';
        do {
            $q = strtolower($row['query']);
            $sh['html'] .= '<li><a href="/blog/search/?q=' . urlencode($q) . '">' . htmlentities(ucfirst($q), ENT_COMPAT, 'UTF-8') . "</a></li>\n";
        } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
        $sh['html'] .= "</ol>\n";

        return $sh;
    }

    return false;
}
