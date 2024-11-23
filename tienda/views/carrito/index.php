<h1>Carrito de la compra</h1>
<?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) >= 1) : ?>
  <table>
    <thead>
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Unidades</th>
        <th>Precio</th>
        <th>Eliminar</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($carrito as $key => $value) :
        $producto = $value['producto'];
        ?>

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
          <td>
            <?= $value['unidades']; ?>
            <div class="unidades">
              <a href="<?= URL_BASE; ?>Carrito/mas&index=<?= $key; ?>" class="btn">+</a>
              <a href="<?= URL_BASE; ?>Carrito/menos&index=<?= $key; ?>" class="btn btn-accion-eliminar">-</a>
            </div>
          </td>
          <td>$<?= $producto->precio; ?></td>
          <td><a href="<?= URL_BASE; ?>Carrito/remover&index=<?= $key; ?>" class="btn btn-accion-eliminar">Quitar</a></td>
        </tr>

      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <?php $estadi = Util::estadisticaCarrito(); ?>
  <h3 class="total">Precio total: $<?= $estadi['total']; ?></h3>
  <a href="<?= URL_BASE; ?>Pedido/hacer" class="btn btn-pedido">Confirmar pedido</a>
  <a href="<?= URL_BASE; ?>Carrito/eliminarTodo" class="btn btn-accion-eliminar btn-pedido" style="float: left">Vaciar carrito</a>
<?php else : ?>
  <p>El Carrito esta vacío, puedes añadir muchas cosas.</p>
<?php endif; ?>