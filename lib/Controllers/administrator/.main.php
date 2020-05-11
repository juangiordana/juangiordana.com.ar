<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

/**
 * Tell web crawlers we don't want this content to be indexed.
 */
header('X-Robots-Tag: noindex, nofollow');

if (!$act) {
    /**
     * Session has been closed or has expired.
     * Save REQUEST_URI for subsequent session access.
     */
    if (!isset($_SESSION['login_redirect'])) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    }

    header('Location: /sign-in/');
    exit;
}

if ($depth == 1) {
    /**
     * Dashboard.
     */
    $rewrite = __DIR__ . '/dashboard.php';
    return;
} elseif ($depth > 1) {
    /**
     * /.admin/.../
     *
     * Remove "/$level[0]" from $uri.
     */
    $section = substr_replace($uri, '', 0, (strlen($level[0]) + 1));
    $section = __DIR__ . '/' . str_replace('/', '-', $section) . '.php';

    if (is_file($section)) {
        $rewrite = $section;
        return;
    }
}
