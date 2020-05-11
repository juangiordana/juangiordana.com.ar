<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
<script src="<?php echo '/js/2016/functions.js' , JS_CACHE_BUSTER; ?>"></script>

<?php
if (!$app['production']) {
    return;
}
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-289737-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-289737-1');
</script>
<script data-ad-client="ca-pub-1817082179311263" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
