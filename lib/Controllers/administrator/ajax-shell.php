<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

header('Content-Type: application/json');

$command = ( isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '' );

if ($command != '') {
    chdir(APP_PATH);
    $command = escapeshellcmd($command);

    ob_start();

    system($command, $retval);

    $output = ob_get_contents();
    ob_end_clean();

    echo json_encode([
        'command' => $command,
        'output' => $output,
        'retval' => $retval,
    ]);
}
