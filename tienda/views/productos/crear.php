<?php if (isset($editar) && isset($pro) && is_object($pro)): ?>
  <h1>Editar producto: <?= $pro->nombre; ?></h1>
  <?php $url = URL_BASE . 'Producto/guardar&id=' . $pro->id; ?>
<?php else: ?>
  <h1>Agregar nuevo producto</h1>
  <?php $url = URL_BASE . 'Producto/guardar'; ?>
<?php endif; ?>


<form action="<?= $url ?>" method="POST" enctype="multipart/form-data">
  <label for="nombre">Nombre:</label>
  <input type="text" name="nombre" id="nombre" value="<?= isset($pro) && is_object($pro) ? $pro->nombre : ''; ?>" required>

  <label for="descripcion">Descripción:</label>
  <textarea name="descripcion" id="descripcion" cols="30" rows="10" required><?= isset($pro) && is_object($pro) ? $pro->descripcion : ''; ?></textarea>

  <label for="precio">Precio:</label>
  <input type="text" name="precio" id="precio" value="<?= isset($pro) && is_object($pro) ? $pro->precio : ''; ?>" required>

  <label for="stock">Stock:</label>
  <input type="number" name="stock" id="stock" value="<?= isset($pro) && is_object($pro) ? $pro->stock : ''; ?>" required>

  <label for="categoria">Categoria:</label>
  <?php $categorias = Util::mostrarCategorias(); ?>
  <select name="categoria" id="categoria">
    <?php while ($cat = $categorias->fetch_object()): ?>
      <option value="<?= $cat->id; ?>" <?= isset($pro) && is_object($pro) && $cat->id == $pro->categoria_id ? 'selected' : ''; ?>>
        <?= $cat->nombre; ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label for="imagen">Imagen:</label>
  <?php if (isset($pro) && is_object($pro) && !empty($pro->imagen)): ?>
    <img src="<?= URL_BASE; ?>subidas/imagenes/<?= $pro->imagen; ?>" class="miniatura"> <br/>
  <?php endif; ?>
  <input type="file" name="imagen" id="imagen">

  <button type="submit">Guardar</button>
</form>