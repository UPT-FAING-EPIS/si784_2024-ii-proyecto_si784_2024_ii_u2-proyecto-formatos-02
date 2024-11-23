<?php if (isset($pro)) : ?>
  <h1><?= $pro->nombre; ?></h1>
  <div id="detalle-producto">
    <div class="imagen-producto">
      <?php if ($pro->imagen != null) : ?>
        <img src="<?= URL_BASE; ?>subidas/imagenes/<?= $pro->imagen; ?>">
      <?php else : ?>
        <img src="<?= URL_BASE; ?>assets/img/camiseta.png">
      <?php endif; ?>
    </div>
    <div class="datos-producto">
      <p class="descripcion">
        <strong>Descripción del producto:</strong> <br/>
        <?= $pro->descripcion; ?>
      </p>
      <p class="precio">$<?= $pro->precio; ?></p>
      <a href="<?= URL_BASE; ?>Carrito/agregar&id=<?= $pro->id; ?>" class="btn">Comprar</a>
    </div>
  </div>
<?php else : ?>
  <h1>El producto no existe</h1>
<?php endif; ?>