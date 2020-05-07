<?php
require __DIR__ . '/../config.php';

/**
 * Load Twig.
 */
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem([
    APP_PATH . '/lib/views'
]);

$loader->addPath(APP_PATH . '/lib/views/2016', '2016');

$twig = new Twig_Environment($loader);

if (!$app['production']) {
    $twig->addExtension(new Twig_Extension_Debug());
}

$twig->addGlobal('app', $app);
$twig->addGlobal('act', $act);

$twig->addGlobal('_GET', $_GET);
$twig->addGlobal('_POST', $_POST);
$twig->addGlobal('_REQUEST', $_REQUEST);
$twig->addGlobal('_ERROR', ( isset($error) ? $error : null ));

$template = $twig->loadTemplate($app['template'] . '/default/' . basename(__FILE__, '.php') . '.twig');

echo $template->render([]);
