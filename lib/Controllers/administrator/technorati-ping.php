<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

header('Content-Type: text/plain');

/*
 * XML message
 */
$xml  = "\n";
$xml .= '<?xml version="1.0"?>';
$xml .= '<methodCall>';
$xml .= '  <methodName>weblogUpdates.ping</methodName>';
$xml .= '  <params>';
$xml .= '    <param>';
$xml .= '      <value>' . APP_TITLE . '</value>';
$xml .= '    </param>';
$xml .= '    <param>';
$xml .= '      <value>http://www.juangiordana.com.ar/blog/</value>';
$xml .= '    </param>';
$xml .= '  </params>';
$xml .= '</methodCall>';

/*
 * HTTP Request Header
 */
$header = array(
    'Host: rpc.technorati.com',
    'Content-Type: text/xml',
    'Content-length: ' . strlen($xml),
    $xml
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://rpc.technorati.com/rpc/ping');
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

$result = curl_exec($ch);
curl_close($ch);

echo print_r($result, TRUE);
exit;
