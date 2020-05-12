<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

$req = [
    'full_name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
    'captcha' => '',
];

$opt = [
    'phone' => '',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($r, $o, $error) = validateRequest($req, $opt);

    if (!isset($error)) {
        if (!validateEmail($r['email'])) {
            $error['email'] = 1;
        }

        /**
         * Captcha (non-logged in users).
         */
        if (!$act && strlen($r['email']) != $r['captcha']) {
            $error['captcha'] = 1;
        }
    }

    if (!isset($error)) {
        $subject = $r['subject'];

        $text = <<< 'EOD'
- Nombre: %s
- Teléfono: %s
- E-mail: %s

%s
EOD;
        $text = sprintf(
            $text,
            $r['full_name'],
            $o['phone'],
            $r['email'],
            $r['message']
        );

        $html = <<< 'EOD'
<ul>
  <li><strong>Nombre:</strong> %1$s</li>
  <li><strong>Teléfono:</strong> %2$s</li>
  <li><strong>E-mail:</strong> <a href="mailto:%3$s">%3$s</a></li>
</ul>

%4$s
EOD;
        $html = sprintf(
            $html,
            htmlentities($r['full_name']),
            htmlentities($o['phone']),
            htmlentities($r['email']),
            wrapString(htmlentities($r['message']))
        );

        $usr = [
            'email' => $r['email'],
            'email_html' => 1,
            'first_name' => null,
            'full_name' => $r['full_name'],
            'id' => null,
            'last_name' => null,
        ];

        $custom = [
            $subject,
            $text,
            $html,
        ];

        sendMail(1, $usr, $custom);
        sendMail(2, $usr);

        header('Location: ' . $_SERVER['SCRIPT_NAME'] . '?ok=1');
        exit;
    }
}

/**
 * Assign default form values.
 */
foreach (($req + $opt) as $k => $v) {
    $_POST[$k] = ( isset($_POST[$k]) ? $_POST[$k] : $v );
}
unset($k, $v);

$template = $app['template'] . DIRECTORY_SEPARATOR . basename(__DIR__, '.php') . DIRECTORY_SEPARATOR . basename(__FILE__, '.php') . '.twig';
