<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
  <header id="header">
    <nav id="navbar" class="navbar navbar-default">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

          <a class="navbar-brand" href="/">
            <svg width="50px" height="50px" viewBox="0 0 140 140" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="logo">
              <g>
                <path d="M70,140 C108.659934,140 140,108.659934 140,70 C140,31.3400656 108.659934,0 70,0 C31.3400656,0 0,31.3400656 0,70 C0,108.659934 31.3400656,140 70,140 Z" class="outer-ring"></path>
                <path d="M70,120 C97.6142389,120 120,97.6142389 120,70 C120,42.3857611 97.6142389,20 70,20 C42.3857611,20 20,42.3857611 20,70 C20,97.6142389 42.3857611,120 70,120 Z" class="middle-ring"></path>
                <path d="M70,95 C83.8071194,95 95,83.8071194 95,70 C95,56.1928806 83.8071194,45 70,45 C56.1928806,45 45,56.1928806 45,70 C45,83.8071194 56.1928806,95 70,95 Z" class="center-ring"></path>
                <path d="M70,71 C106.050248,71.6177586 140,89.9291525 140,70 C140,31.3400656 108.659934,0 70,0 C31.3400656,0 0,31.3400656 0,70 C0,88.7307819 36.1175085,70.4193881 70,71 Z" class="shine"></path>
              </g>
            </svg>
            Juan Giordana
          </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li><a class="active" href="/about/">Acerca <span class="sr-only">(actual)</span></a></li>
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/contact/">Contacto</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>