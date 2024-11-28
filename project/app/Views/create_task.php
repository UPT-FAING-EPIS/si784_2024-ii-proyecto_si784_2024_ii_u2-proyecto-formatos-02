<?php if (!isset($_SESSION['user'])): ?>
    <?php header('Location: /login'); exit; ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Tarea</title>
</head>
<body>
    <div class="create-task-container">
        <h1>Crear Nueva Tarea</h1>

        <?php if (isset($error_message)): ?>
            <div style="color: red;"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="/task/create" method="POST">
            <label for="title">Título:</label>
            <input type="text" name="title" id="title" required value="<?= isset($data['title']) ? htmlspecialchars($data['title']) : '' ?>"><br><br>

            <label for="description">Descripción:</label>
            <textarea name="description" id="description"><?= isset($data['description']) ? htmlspecialchars($data['description']) : '' ?></textarea><br><br>

            <label for="category_id">Categoría:</label>
            <select name="category_id" id="category_id">
                <?php 
                // Verifica si las categorías están disponibles
                if (isset($categories) && is_array($categories)) {
                    foreach ($categories as $category):
                ?>
                    <option value="<?= $category['id'] ?>" <?= (isset($data['category_id']) && $data['category_id'] == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
                <?php } ?>
            </select><br><br>

            <label for="due_date">Fecha de vencimiento:</label>
            <input type="date" name="due_date" id="due_date" value="<?= isset($data['due_date']) ? htmlspecialchars($data['due_date']) : '' ?>"><br><br>

            <button type="submit">Crear Tarea</button>
        </form>

        <a href="/dashboard">Volver al Dashboard</a>
    </div>
</body>
</html>
