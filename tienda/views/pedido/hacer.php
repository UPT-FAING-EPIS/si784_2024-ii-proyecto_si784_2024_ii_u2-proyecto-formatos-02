<?php if (isset($_SESSION['identidad'])) : ?>
  <h1>Hacer pedido</h1>
  <h3>Dirección para el envío</h3>
  <form action="<?= URL_BASE; ?>Pedido/agregar" method="POST">
    <label>Provincia: <input type="text" name="provincia" required></label>
    <label>Localidad: <input type="text" name="localidad" required></label>
    <label>Dirección: <input type="text" name="direccion" required></label>
    <button type="submit">Confirmar pedido</button>
  </form>

<?php else : ?>
  <h1>Inicie Sessión</h1>
  <p>Necesita iniciar sessión para realizar el pedido.</p>
<?php endif; ?>