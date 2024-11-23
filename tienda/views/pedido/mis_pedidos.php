<?php if (isset($gestion)) : ?>
  <h1>Gestionar pedidos</h1>
<?php else : ?>
  <h1>Mis pedidos</h1>
<?php endif; ?>
<table>
  <thead>
    <tr>
      <th>N° de pedido</th>
      <th>Costo</th>
      <th>Fecha</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($pedido = $pedidos->fetch_object()) : ?>
      <tr>
        <td><a href="<?= URL_BASE; ?>Pedido/detalle&id=<?= $pedido->id; ?>"><?= $pedido->id; ?></a></td>
        <td>$<?= $pedido->coste; ?></td>
        <td><?= $pedido->fecha; ?></td>
        <td><?= Util::mostrarEstado($pedido->estado); ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>