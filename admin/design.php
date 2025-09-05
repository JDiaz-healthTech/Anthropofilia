<form method="post">
  <label>Color fondo del sitio:
    <input type="color" name="color_site_bg" value="<?= htmlspecialchars($color_site_bg) ?>">
  </label>
  <label>Color fondo del contenido:
    <input type="color" name="color_content_bg" value="<?= htmlspecialchars($color_content_bg) ?>">
  </label>
  <button type="submit">Guardar</button>
</form>
<div class="preview" style="--bg: <?= $color_site_bg ?>; --bg-content: <?= $color_content_bg ?>;">
  <article class="card">Vista previa de contenido</article>
</div>
