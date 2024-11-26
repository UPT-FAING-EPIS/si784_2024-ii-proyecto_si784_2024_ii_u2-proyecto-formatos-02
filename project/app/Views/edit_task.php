<?php if (!isset($_SESSION['user'])): ?> 
    <?php header('Location: /login'); exit; ?>
<?php endif; ?>

<?php
// Obtener el ID de la tarea que se va a editar
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
</head>
<body>
    <div class="edit-task-container">
        <h1>Editar Tarea</h1>
        <?php if ($task): ?>
            <form action="/task/edit/<?php echo $task['id']; ?>" method="POST">
                <label for="title">Título:</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($task['title']); ?>" required><br><br>

                <label for="description">Descripción:</label>
                <textarea name="description" id="description"><?php echo htmlspecialchars($task['description']); ?></textarea><br><br>

                <label for="category_id">Categoría:</label>
                <select name="category_id" id="category_id">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $task['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="due_date">Fecha de vencimiento:</label>
                <input type="date" name="due_date" id="due_date" value="<?php echo $task['due_date']; ?>"><br><br>

                <label for="is_completed">Completada:</label>
                <input type="checkbox" name="is_completed" id="is_completed" value="1" <?php echo $task['is_completed'] ? 'checked' : ''; ?>><br><br>

                <button type="submit">Actualizar Tarea</button>
            </form>

        <?php else: ?>
            <p>Tarea no encontrada.</p>
        <?php endif; ?>
        <a href="/dashboard">Volver al Dashboard</a>
    </div>
</body>
</html>
