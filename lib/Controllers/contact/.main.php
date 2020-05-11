<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if ($depth == 1) {
    /**
     * /contact/
     */
    if ($level[0] == 'contact') {
        $rewrite = __DIR__ . '/contact.php';
        return;
    }
}
