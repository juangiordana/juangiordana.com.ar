<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateField($data)
{
    if (is_array($data)) {
        foreach ($data as &$v) {
            $v = validateField($v);
        }
        unset($v);

        return $data;
    }

    return trim($data);
}

function validateUrl($string)
{
    if (!$url = @parse_url($string)) {
        return false;
    }

    if (!empty($url['port']) ||
        !empty($url['user']) ||
        !empty($url['pass']) ||
        !in_array($url['scheme'], ['http', 'https'])
    ) {
        return false;
    }

    return $string;
}

function validateRequest($req = [], $opt = [])
{
    /*
     * Validate required fields.
     */
    foreach ($req as $k => &$v) {
        if (!isset($_POST[$k])) {
            $error[$k] = 1;
        } else {
            $r[$k] = validateField($_POST[$k]);
            if ($r[$k] == '') {
                $error[$k] = 1;
            }
        }
    }
    unset($k, $v);

    /*
     * Validate optional fields.
     */
    foreach ($opt as $k => &$v) {
        if (isset($_POST[$k])) {
            $o[$k] = validateField($_POST[$k]);
        } else {
            $o[$k] = '';
        }
    }
    unset($k, $v);

    return [
        isset($r) ? $r : null,
        isset($o) ? $o : null,
        isset($error) ? $error : null,
    ];
}

function wrapString($string)
{
    $string = str_replace(["\n\n", "\r\n\r\n"], '</p><p>', $string);
    $string = nl2br($string);
    $string = str_replace('</p>', "</p>\n", $string);
    return '<p>' . $string . '</p>';
}

function generateUri($string)
{
    /*
     * Remove PHP/HTML/JavaScript tags from Query String.
     * All lowercase.
     * Replace accented and latin (?) characters.
     * - Kill entities.
     * - Remove non-alphanumeric characters except ' _-'.
     * - Replace spaces with dashes.
     * - Remove continuous dashes.
     * Remove dashes at the beginning and end of the string.
     */
    $string = strip_tags($string);
    $string = strtolower($string);
    $string = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
        ['a', 'e', 'i', 'o', 'u', 'n'],
        $string
    );

    $string = preg_replace(
        ['/&.+?;/', '/[^a-z0-9 \._-]/', '/\s+/', '/-+/'],
        ['', '', '-', '-'],
        $string
    );

    $uri = trim($string, '-');

    return ( !empty($uri) ? $uri : false );
}

require __DIR__ . '/cookies.php';
require __DIR__ . '/e-mail.php';
