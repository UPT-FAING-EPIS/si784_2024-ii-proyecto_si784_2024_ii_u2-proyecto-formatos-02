<h1>Gestionar categorias</h1>

<a href="<?= URL_BASE; ?>Categoria/crear" class="btn btn-small">Nueva categoria</a>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($cat = $categorias->fetch_object()): ?>
    <tr>
      <td><?= $cat->id; ?></td>
      <td><?= $cat->nombre; ?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>