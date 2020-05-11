<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if ($depth == 4) {
    /**
     * /level0/level1/level2/level3/
     */
    if ($level[0] == 'something' and $level[1] == 'else') {
        $rewrite = __DIR__ . '/something-else.php';
        return;
    }
} elseif ($depth == 3) {
} elseif ($depth == 2) {
} elseif ($depth == 1) {
}
