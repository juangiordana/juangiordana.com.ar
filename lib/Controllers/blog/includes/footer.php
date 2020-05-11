<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
<footer>
  <div class="container">
    <hr>
    <div class="row">
      <div class="col-md-6">
        <p>
          <small>&copy;<?php echo idate('Y'); ?> <a href="/">Juan Giordana</a>.</small>
        </p>
      </div>
      <div class="col-md-6">
        <nav>
          <ul class="nav navbar-nav">
            <li><a href="/sitemap/">Sitemap</a></li>
            <li><a href="/contact/">Contacto</a></li>
            <li>
              <a href="https://github.com/juangiordana" rel="me" target="_blank" title="Juan Giordana en GitHub">
                <img alt="" src="/img/github.png">
                GitHub
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</footer>
