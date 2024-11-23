<h1>Detalle del pedido</h1>
<?php if (isset($pedido)) : ?>
  <?php if (isset($_SESSION['admin'])) : ?>
    <h3>Cambiar estado del pedido</h3>
    <form action="<?= URL_BASE; ?>Pedido/estado" method="POST">
      <input type="hidden" value="<?= $pedido->id; ?>" name="pedido_id">
      <select name="estado">
        <option value="pendiente" <?= $pedido->estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
        <option value="preparacion" <?= $pedido->estado == 'preparacion' ? 'selected' : ''; ?>>En preparación</option>
        <option value="preparado" <?= $pedido->estado == 'preparado' ? 'selected' : ''; ?>>Preparado para enviar</option>
        <option value="enviado" <?= $pedido->estado == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
      </select>
      <button type="submit">Guardar</button>
    </form>
  <?php endif; ?>

  <br />
  <h3>Datos del envío</h3>
  <br />
  <p>Provincia: <strong><?= $pedido->provincia; ?></strong></p>
  <p>Localidad: <strong><?= $pedido->localidad; ?></strong></p>
  <p>Dirección: <strong><?= $pedido->direccion; ?></strong></p>
  <br />
  <h3>Datos del pedido</h3>
  <br />
  <p>Estado del pedido: <strong><?= Util::mostrarEstado($pedido->estado); ?></strong></p>
  <p>Número del pedido: <strong><?= $pedido->id; ?></strong></p>
  <p>Total a pagar: <strong>$<?= $pedido->coste; ?></strong></p>
  <br />
  <p>Detalles de los productos:</p>
  <br />
  <table>
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Unidades</th>
        <th>Precio</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($producto = $productos->fetch_object()) : ?>
        <tr>
          <td>
            <?php if ($producto->imagen != null) : ?>
              <img src="<?= URL_BASE; ?>subidas/imagenes/<?= $producto->imagen; ?>" class="img_carrito">
            <?php else : ?>
              <img src="<?= URL_BASE; ?>assets/img/camiseta.png" class="img_carrito">
            <?php endif; ?>
          </td>
          <td>
            <a href="<?= URL_BASE; ?>Producto/ver&id=<?= $producto->id; ?>">
              <?= $producto->nombre; ?>
            </a>
          </td>
          <td><?= $producto->unidades; ?></td>
          <td><?= $producto->precio; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>