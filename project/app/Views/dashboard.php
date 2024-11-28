<?php

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

// Suponiendo que ya tienes el ID de usuario en la sesión
$userId = $_SESSION['user']; 

// Aquí deberías obtener las tareas y notificaciones del usuario desde la base de datos
// Ejemplo:
// $tasks = obtenerTareas($userId); 
// $notifications = obtenerNotificaciones($userId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/styles_dashboard.css">
    <style>
        /* Centrar los botones y el contenido */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
            position: relative;
            flex-direction: column;
        }

        .dashboard-container {
            text-align: center;
            max-width: 800px;
        }

        .btn-toggle {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-toggle:hover {
            background-color: #388E3C;
        }

        .btn-logout {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #FF0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-logout:hover {
            background-color: #CC0000;
        }

        /* Panel flotante de notificaciones (izquierda) */
        #floating-notifications {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            background: #4CAF50;
            color: white;
            border: 2px solid #388E3C;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #floating-notifications ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #floating-notifications li {
            margin: 10px 0;
            padding: 10px;
            background-color: #388E3C;
            border-radius: 5px;
        }

        #floating-notifications button {
            background: none;
            border: none;
            font-size: 16px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 10px;
            color: #fff;
            cursor: pointer;
        }

        #floating-notifications button:hover {
            color: #FFEB3B;
        }

        /* Panel de gestión de notificaciones (derecha) */
        .notifications-container {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            width: 300px;
            max-height: 500px;
            overflow-y: auto;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .notifications-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .notifications-container th, .notifications-container td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        .btn-create-task,
        .btn-create-notification {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 10px auto;
        }

        .btn-create-task:hover,
        .btn-create-notification:hover {
            background-color: #388E3C;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
    </style>
</head>
<body>
    <!-- Contenedor Central -->
    <div class="dashboard-container">
        <h1>Bienvenido a tu Dashboard</h1>

        
        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <button id="show-floating" class="btn-toggle">Mostrar Notificaciones</button>
        <button id="show-notifications-section" class="btn-toggle">Mostrar Gestión de Notificaciones</button>

        <!-- Tareas -->
        <h2>Tareas</h2>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Fecha de vencimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tasks)): ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['title']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td><?php echo htmlspecialchars($task['category_name']); ?></td>
                            <td><?php echo $task['is_completed'] ? 'Completada' : 'Pendiente'; ?></td>
                            <td><?php echo $task['due_date']; ?></td>
                            <td>
                                <a href="/task/edit/<?php echo $task['id']; ?>">Editar</a>
                                <a href="/task/delete/<?php echo $task['id']; ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No tienes tareas asignadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button class="btn-create-task" onclick="window.location.href='/task/create';">Crear Nueva Tarea</button>
        <button class="btn-logout" onclick="window.location.href='/logout';">Cerrar Sesión</button>
    </div>

    <!-- Notificaciones Flotantes (Izquierda) -->
    <div id="floating-notifications">
        <h3>Notificaciones</h3>
        <ul>
            <!-- Aquí se llenarán las notificaciones con PHP -->
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No tienes notificaciones.</li>
            <?php endif; ?>
        </ul>
        <button onclick="closeNotifications()">X</button>
    </div>

    <!-- Gestión de Notificaciones (Derecha) -->
    <div class="notifications-container">
        <h2>Gestión de Notificaciones</h2>
        <table>
            <thead>
                <tr>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($notification['message']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></td>
                            <td><?php echo $notification['is_read'] ? 'Leída' : 'No leída'; ?></td>
                            <td>
                                <?php if (!$notification['is_read']): ?>
                                    <a href="/notification/mark-read/<?php echo $notification['id']; ?>">Marcar como leída</a>
                                <?php endif; ?>
                                <a href="/notification/delete/<?php echo $notification['id']; ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No tienes notificaciones.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Botón para crear notificación -->
        <form action="/notification/create" method="POST">
            <textarea name="message" required placeholder="Escribe tu notificación aquí"></textarea>
            <button type="submit" class="btn-create-notification">Crear Nueva Notificación</button>
        </form>
    </div>

    <script>
        // Mostrar/Ocultar notificaciones flotantes
        document.getElementById('show-floating').addEventListener('click', function () {
            const floatingNotifications = document.getElementById('floating-notifications');
            floatingNotifications.style.display = floatingNotifications.style.display === 'none' ? 'block' : 'none';
        });

        // Mostrar/Ocultar gestión de notificaciones
        document.getElementById('show-notifications-section').addEventListener('click', function () {
            const notificationsContainer = document.querySelector('.notifications-container');
            notificationsContainer.style.display = notificationsContainer.style.display === 'none' ? 'block' : 'none';
        });

        function closeNotifications() {
            const floatingNotifications = document.getElementById('floating-notifications');
            floatingNotifications.style.display = 'none';
        }
    </script>
</body>
</html>
