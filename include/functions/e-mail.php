<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

/**
 * Replace using array keys as patterns and values as replacements.
 *
 * This function returns a string or an array with all the occurrences of the
 * patterns found in the keys of $array replaced with their respective values.
 *
 * @param array $array Associative array with patterns as keys and replacements as values.
 * @param mixed $string String or array being searched and replaced on.
 * @return mixed Returns a string or an array with the replaced values.
 */
function keys2values(array $array, $string)
{
    foreach ($array as $k => &$v) {
        $patterns[] = $k;
        $replacements[] = $v;
    }
    unset($k, $v);

    return str_replace($patterns, $replacements, $string);
}

function sendMail($id, $usr = [], $custom = [], $return = false)
{
    global $app, $dbh;

    /**
     * Get e-mail template.
     */
    $query = <<< 'EOD'
SELECT
    `from`,
    `to`,
    `reply_to`,
    `cc`,
    `bcc`,
    `priority`,
    `subject`,
    `message_text`,
    `message_html`,
    `format_html`
FROM
    `emails`
WHERE
    `id` = %d
LIMIT 1
EOD;
    $query = sprintf($query, $id);
    $stmt = $dbh->query($query);
    if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return false;
    }

    $email = $row;

    /**
     * Recipient information.
     *
     * $usr = [
     *     'email_html' => 1,
     *     'email' => '...',
     *     'full_name' => '...',
     * ];
     */

    /**
     * Set data as a pattern => replacement array.
     * $data[{PATTERN}] = $replacement.
     */
    $data = [
        '{APP_EMAIL}' => $app['email'],
        '{APP_DOMAIN}' => $app['domain'],
        '{APP_NAME}' => $app['name'],
        '{APP_URL}' => $app['url'],
        '{DATE_Y}' => date('Y'),
        '{EMAIL_CONTACT}' => $app['email_contact'],
        '{USER_EMAIL}' => $usr['email'],
        '{USER_FIRST_NAME}' => $usr['first_name'],
        '{USER_FULL_NAME}' => $usr['full_name'],
        '{USER_ID}' => $usr['id'],
        '{USER_LAST_NAME}' => $usr['last_name'],
    ];

    foreach ($custom as $k => &$v) {
        $data['{CUSTOM' . $k . '}'] = $v;
    }
    unset($k, $v);

    $pattern = '/(.+) \<(.+)\>/';

    foreach (['from', 'to', 'reply_to', 'cc', 'bcc'] as $v) {
        if (preg_match($pattern, $email[$v], $matches)) {
            $email[$v] = [
                keys2values($data, $matches[2]) => keys2values($data, $matches[1])
            ];
        } else {
            $email[$v] = keys2values($data, $email[$v]);
        }
    }
    unset($pattern, $v);

    /**
     * VERP.
     *
    $verp = explode('@', $app['email_error']);
    $envelope = ( is_array($email['to']) ? key($email['to']) : $email['to'] );
    $email['return-path'] = $verp[0] . '-' . str_replace('@', '=', $envelope) . '@' . $verp[1];
    unset($verp, $envelope);
     */
    $email['return-path'] = $app['email_bounce'];

    $subject = keys2values($data, $email['subject']);

    /**
     * Load template.
     */
    $template = file_get_contents(APP_PATH . '/lib/Views/emails/email-01.txt');

    $text = [
        '{APP_DOMAIN}' => $app['domain'],
        '{APP_NAME}' => $app['name'],
        '{APP_URL}' => $app['url'],
        '{CONTENT}' => keys2values($data, $email['message_text']),
        '{DATE_Y}' => date('Y'),
        '{EMAIL_CONTACT}' => $app['email_contact'],
        '{SUBJECT}' => $subject,
    ];

    $text = strip_tags(keys2values($text, $template));

    if ($email['format_html'] && $usr['email_html']) {
        $template  = file_get_contents(APP_PATH . '/lib/Views/emails/email-03.html');

        $html = [
            '{APP_DOMAIN}' => $app['domain'],
            '{APP_NAME}' => $app['name'],
            '{APP_URL}' => $app['url'],
            '{CONTENT}' => keys2values($data, $email['message_html']),
            '{DATE_Y}' => date('Y'),
            '{EMAIL_CONTACT}' => htmlspecialchars($app['email_contact']),
            '{SUBJECT}' => htmlspecialchars($subject),
        ];

        $html = keys2values($html, $template);
    } else {
        $html = '';
    }

    if ($return) {
        return [
            'From' => $email['from'],
            'To' => $email['to'],
            'Reply-to' => $email['reply_to'],
            'Cc' => $email['cc'],
            'Bcc' => $email['bcc'],
            'Return-Path' => $email['return-path'],
            'Subject' => $subject,
            'Message' => [$text, $html],
            'html' => ( $email['format_html'] ? true : false )
        ];
    }

    /**
     * Swift Mailer:
     * Create the Transport.
     * Create the Mailer using the Transport created.
     */
    $transport = Swift_SmtpTransport::newInstance('localhost', (PRODUCTION ? 25 : 1025));
    $mailer = Swift_Mailer::newInstance($transport);

    /**
     * Use the 'array' cache type.
     */
    Swift_Preferences::getInstance()->setCacheType('array');

    /**
     * Create a Message.
     */
    $message = Swift_Message::newInstance();

    /**
     * Sender and Recipient details.
     */
    $message->setSender($app['email']);

    $message->setFrom($email['from']);

    $message->setTo($email['to']);

    if (!empty($email['reply_to'])) {
        $message->setReplyTo($email['reply_to']);
    }

    if (!empty($email['cc'])) {
        $message->setCc($email['cc']);
    }

    if (!empty($email['bcc'])) {
        $message->setBcc($email['bcc']);
    }

    $message->setReturnPath($email['return-path']);

    /**
     * Set Message priority.
     */
    $message->setPriority($email['priority']);

    /**
     * Give the message a subject, a body and (optionally) an alternative body.
     */
    $message->setSubject($subject);

    $message->setBody($text, 'text/plain');

    if ($html !== '') {
        $message->addPart($html, 'text/html');
    }

    /**
     * E-mail delivery.
     */
    return $mailer->send($message);
}
