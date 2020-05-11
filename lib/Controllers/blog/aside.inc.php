<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}
?>
<aside>
  <form action="/blog/search/">
    <fieldset>
      <input type="text" name="q" maxlength="255" value="" placeholder="Palabra o frase" required>
      <input type="submit" value="Buscar">
    </fieldset>
  </form>

  <h3>Categorías:</h3>
  <?php echo $category['html']; ?>

  <h3>Últimas búsquedas:</h3>
  <?php echo $searchHistory['html']; ?>

  <h3>Archivo:</h3>
  <?php echo $archive['html']; ?>
</aside>
