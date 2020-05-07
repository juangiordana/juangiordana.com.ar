<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

/**
 * Cookies Management
 */
function cookieInsert($name, $email, $url)
{
    global $app, $dbh;

    $expDate = strtotime('+1 month');

    $query = 'INSERT INTO `visitors` SET `exp_date` = ?, `name` = ?, `email` = ?, `url` = ?';
    $stmt = $dbh->prepare($query);
    $stmt->execute([$expDate, $name, $email, $url]);

    $id = $dbh->lastInsertId();
    $uuid = sha1(sha1($app['cookie_salt']) . $id);

    $query = 'UPDATE `visitors` SET `uuid` = ? WHERE `id` = ? LIMIT 1';
    $stmt = $dbh->prepare($query);
    $stmt->execute([$uuid, $id]);

    setcookie('uuid', $uuid, $expDate, $app['cookie_path'], $app['cookie_domain'], false);

    return $uuid;
}

function cookieUpdate($uuid, $name, $email, $url)
{
    global $app, $dbh;

    if ($c = cookieValidate($uuid)) {
        $expDate = strtotime('+1 month');

        $query = 'UPDATE `visitors` SET `exp_date` = ?, `name` = ?, `email` = ?, `url` = ? WHERE `id` = ? LIMIT 1';
        $stmt = $dhb->prepare($query);
        $stmt->execute([$expDate, $name, $email, $url, $c['id']]);

        setcookie('uuid', $uuid, $expDate, $app['cookie_path'], $app['cookie_domain'], false);

        return $uuid;
    }

    return false;
}

function cookieDelete($uuid)
{
    global $app, $dbh;

    if ($c = cookieValidate($uuid)) {
        $query = 'DELETE FROM `visitors` WHERE `id` = ? LIMIT 1';
        $stmt = $dbh->prepare($query);
        $stmt->execute([$c['id']]);

        setcookie('uuid', false, $c['exp_date'], $app['cookie_path'], $app['cookie_domain'], false);

        return true;
    }

    return false;
}

function cookieValidate($uuid)
{
    global $dbh;

    $query = 'SELECT `id`, `uuid`, `exp_date`, `name`, `email`, `url` FROM `visitors` WHERE `uuid` = ? LIMIT 1';
    $stmt = $dbh->prepare($query);
    $stmt->execute([$uuid]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row;
    }

    return false;
}
