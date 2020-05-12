<?php
require __DIR__ . '/config.php';

/**
 * Dynamic Content
 *  Get PATH part from REQUEST_URI.
 *  Basic hack protections.
 *  Remove slashes from the beginning and end of the string.
 *  Fix trailing slash problem: Keep it /'d.
 *  Split REQUEST_URI at the slashes.
 *  Look if anything in the application matches the request.
 */
$home = $_SERVER['REQUEST_URI'] == '/';
$path = parse_url($_SERVER['REQUEST_URI']);
$uri = strip_tags($path['path']);
$uri = strtolower($uri);
$uri = trim($uri, '/');

$http['301'] = ( !$home && strcmp($path['path'], "/$uri/") != 0 );
$http['404'] = false;

/**
 * Make pear/Pager custom links work.
 */
if (preg_match('/\/(?:page|pagina)?(?:\-)?([0-9]+)$/', $uri, $matches)) {
    $_GET['pageID'] = $_REQUEST['pageID'] = $matches[1];
    // $uri = substr($uri, 0, strrpos($uri, '/'));
}
unset($matches);

$level = explode('/', $uri);
$depth = count($level);

if (!$home) {
    foreach ($level as &$v) {
        $v = trim($v);
        if (empty($v)) {
            $http['404'] = true;
            break;
        }
    }
    unset($v);
}

/**
 * Sections.
 *  Sections refers to content that doesn't change data.
 *
 * Modules.
 *  Every resource that receives GET or POST data must be a module.
 */
if (!$http['404']) {
    $modules = [
        '.admin' => 'administrator',
        'blog' => 'blog',
        'captcha' => 'captcha',
        'contact' => 'contact',
        'buscar' => 'search',

        'sign-in' => 'sign-in',
        'sign-out' => 'sign-out',
        'sign-up' => 'sign-up',
    ];

    $section = APP_PATH . '/lib/Controllers/' . ( $home ? 'index' : str_replace('/', '-', $uri) ) . '.php';

    if (is_file($section)) {
        $rewrite = $section;
    } elseif (isset($modules[$level[0]])) {
        require APP_PATH . '/lib/Controllers/' . $modules[$level[0]] . '/.main.php';
    } else {
        require APP_PATH . '/lib/Controllers/rewrite/.main.php';
    }

    if (isset($rewrite)) {
        if ($http['301']) {
            header('Location: ' . $app['url'] . $uri . '/', true, 301);
            exit;
        }

        require $rewrite;

        /**
         * Twig.
         */
        if (isset($template)) {
            $loader = new \Twig\Loader\FilesystemLoader(APP_PATH . '/lib/Views');
            $loader->addPath(APP_PATH . '/lib/Views/2016', '2016');
            $loader->addPath(APP_PATH . '/lib/views/administrator', 'administrator');

            $twig = new \Twig\Environment($loader, [
                'debug' => !$app['production'],
            ]);

            if (!$app['production']) {
                $twig->addExtension(new Twig_Extension_Debug());
            }

            $twig->addGlobal('app', $app);
            $twig->addGlobal('act', $act);

            $twig->addGlobal('pager', ( isset($pager) ? $pager : null ));

            $twig->addGlobal('_GET', $_GET);
            $twig->addGlobal('_POST', $_POST);
            $twig->addGlobal('_REQUEST', $_REQUEST);
            $twig->addGlobal('_ERROR', ( isset($error) ? $error : null ));

            echo $twig->render(
                $template,
                ( isset($templateVars) ? $templateVars : [] )
            );
        }
        return;
    }

    /**
     * Fix broken links.
     *  Prevent parsing too long strings with the regular expressions engine.
     */
    if (strlen($_SERVER['REQUEST_URI']) < 100) {
        require APP_PATH . '/lib/Controllers/rewrite/link-rot.php';
    }
}

header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
include APP_PATH . '/www/404.html';
exit;
