<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

if (!empty ($_POST)) {
    foreach ($_POST['r'] as $k => $v) {
        if (!$r[$k] = validateField ($v, FALSE, FALSE)) {
            $error[$k] = 1;
        }
    }

    if (!isset($error)) {
        $r['password']  = md5($r['password']);
        $r['password1'] = md5($r['password1']);
        $r['password2'] = md5($r['password2']);

        $sql = sprintf ('SELECT id FROM users WHERE id = %d AND password = \'%s\' LIMIT 1', $_SESSION['admin']['id'], $r['password']);
        $result = mysql_unbuffered_query ($sql);
        if (!$row = mysql_fetch_assoc ($result)) {
            $error['password'] = 1;
        }

        if ($r['password1'] != $r['password2']) {
            $error['password_match'] = 1;
            $error['password1'] = 1;
            $error['password2'] = 1;
        }
    } else {
        $error['password_new'] = 1;
    }

    if (!isset($error)) {
        $sql = sprintf ('UPDATE users SET password = \'%s\' WHERE id = %d LIMIT 1', $r['password1'], $_SESSION['admin']['id']);
        mysql_query($sql);

         $_SESSION['admin']['password'] = $r['password2'];

        header ('Location: ' . $_SERVER['PHP_SELF'] . '?ok=1');
        exit;
    } else {
        foreach ($error as $key => $value) {
            $error[$key] = 'error';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title><?php echo ADMIN_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="/css/admin.css" />
</head>
<body>
<div id="wrapper">
<?php include __DIR__ . '/header.inc.php'; ?>
  <div id="content">
    <h2>Change Password.</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <dl class="forms">
        <dt>
          <label for="password">* Current Password:</label>
        </dt>
        <dd>
          <input  type="password" name="r[password]" id="password" maxlength="255" class="text <?php echo isset($error['password']) ? $error['password'] : FALSE; ?>" />
        </dd>
      </dl>
      <dl class="forms">
        <dt>
          <label for="password1">* New Password:</label>
        </dt>
        <dd>
          <input type="password" name="r[password1]" id="password1" maxlength="255" class="text <?php echo isset($error['password1']) ? $error['password1'] : FALSE; ?>" />
        </dd>
        <dt>
          <label for="password2">* Retype New Password:</label>
        </dt>
        <dd>
          <input type="password" name="r[password2]" id="password2" maxlength="255" class="text <?php echo isset($error['password1']) ? $error['password1'] : FALSE; ?>" />
        </dd>
      </dl>
      <p><input type="submit" name="submit" value="Submit" /></p>
    </form>
<?php
if (!empty($error)) {
    echo '<dl>';
    echo '<dt><span class="b error">Error:</span></dt>';
    echo ($error['password_new']) ? '<dd>Required fields cannot be left empty.</dd>' : FALSE;
    echo $error['password'] ? '<dd>Invalid password for user <strong>' . $_SESSION['admin']['username'] . '</strong>.</dd>' : FALSE;
    echo ($error['password_match']) ? '<dd> New Passwords doesn\'t match.</dd>' : FALSE;
    echo '</dl>';
}
if (isset($_GET['ok'])) {
    echo '<p class="success">Password Updated for user <strong>' . $_SESSION['admin']['username'] . '</strong>.</p>';
}
?>
  </div>
<?php
include __DIR__ . '/navbar.inc.php';
include __DIR__ . '/footer.inc.php';
?>
</div>
</body>
</html>
