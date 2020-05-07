<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
<!DOCTYPE html>
<html class="no-js" dir="ltr" lang="es-AR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0" />
<title><?php echo $title . ' - ' . $app['title']; ?></title>
<link href="/blog/feeds/atom/" rel="alternate" title="<?php echo $app['title'] , ' - Atom feeds'; ?>" type="application/atom+xml" />
<link href="/blog/feeds/rss/" rel="alternate" title="<?php echo $app['title'] , ' - RSS feeds'; ?>" type="application/rss+xml" />
<?php echo $archive['link']; ?>
<?php include __DIR__ . '/includes/css.php'; ?>
<?php include __DIR__ . '/includes/js-head.php'; ?>
</head>
<body>
<div id="wrapper">

  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <?php echo $posts; ?>
      </div>
      <div class="col-md-4">
        <?php include __DIR__ . '/aside.inc.php'; ?>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/includes/footer.php'; ?>

</div>
<?php include __DIR__ . '/includes/js-libs.php'; ?>
<?php include __DIR__ . '/includes/js-body.php'; ?>
</body>
</html>
