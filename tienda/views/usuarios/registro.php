<h1>Registrarse</h1>

<?php if (isset($_SESSION['registro']) && $_SESSION['registro'] == 'completado'): ?>
  <strong class="alerta-ok">Registro exitoso</strong>
<?php elseif (isset($_SESSION['registro']) && $_SESSION['registro'] == 'falla'): ?>
  <strong class="alerta-error">Error al registrar, ingrese bien los datos</strong>
<?php endif; ?>
<?php Util::eliminarSession('registro');  ?>

<form action="<?= URL_BASE ?>Usuario/guardar" method="POST">
  <label for="nombre">Nombre:</label>
  <input type="text" name="nombre" id="nombre" required>

  <label for="apellidos">Apellidos:</label>
  <input type="text" name="apellidos" id="apellidos" required>

  <label for="email">Email:</label>
  <input type="email" name="email" id="email" required>

  <label for="password">Contraseña:</label>
  <input type="password" name="password" id="password" required>

  <button type="submit">Registrase</button>
</form>