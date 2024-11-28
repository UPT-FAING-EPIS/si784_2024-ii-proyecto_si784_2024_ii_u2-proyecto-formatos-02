<?php foreach ($tasks as $task): ?>
    <div>
        <h3><?= htmlspecialchars($task['title']) ?></h3>
        <p><?= htmlspecialchars($task['description']) ?></p>
        <p>Categoria: <?= htmlspecialchars($task['category']) ?></p>
        <p>Fecha de vencimiento: <?= htmlspecialchars($task['due_date']) ?></p>
        <p>Estado: <?= $task['is_completed'] ? 'Completada' : 'Pendiente' ?></p>
        <a href="/task/edit/<?= $task['id'] ?>">Editar</a>
        <a href="/task/delete/<?= $task['id'] ?>">Eliminar</a>
    </div>
<?php endforeach; ?>
