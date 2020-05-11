<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if ($depth == 1) {
    $rewrite = __DIR__ . '/sign-up.php';
    return;
}
