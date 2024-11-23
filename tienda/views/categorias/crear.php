<h1>Crear nueva categoria</h1>

<form action="<?= URL_BASE; ?>Categoria/guardar" method="POST">
  <label for="nombre">Nombre de la categoria:</label>
  <input type="text" name="nombre" id="nombre" required>

  <button type="submit">Crear</button>
</form>