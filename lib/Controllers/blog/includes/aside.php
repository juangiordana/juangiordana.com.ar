<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
<aside id="aside">
  <h2>Buscar:</h2>
  <h3>En el Blog:</h3>
  <form action="/blog/search/">
    <fieldset>
      <input type="text" name="q" maxlength="255" value="" placeholder="Palabra o frase" required>
      <input type="submit" value="Buscar">
    </fieldset>
  </form>
</aside>
