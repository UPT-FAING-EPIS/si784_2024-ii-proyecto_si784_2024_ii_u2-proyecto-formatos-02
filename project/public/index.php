<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Controllers\NotificationController; // Agregar controlador de notificaciones
use App\Models\TaskModel;
use App\Models\CategoryModel;
use App\Models\NotificationModel; // Agregar modelo de notificaciones

session_start(); // Iniciar la sesión

// Instancia del controlador de autenticación
$authController = new AuthController(); 

// Instancia del modelo y controlador de notificaciones
$notificationModel = new NotificationModel(); 
$notificationController = new NotificationController($notificationModel); 

// Instancia del modelo de tareas y categorías
$taskModel = new TaskModel();
$categoryModel = new CategoryModel();
$taskController = new TaskController($taskModel, $categoryModel);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si no está autenticado, redirigir a login
if (!isset($_SESSION['user']) && $uri !== '/login' && $uri !== '/register') {
    header('Location: /login');
    exit;
}

// Redirigir automáticamente a /login si se accede a la raíz
if ($uri === '/' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Location: /login');
    exit;
}

// Manejo de rutas para login
if ($uri === '/login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->showLogin();
} elseif ($uri === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login($_POST);
}

// Manejo de rutas para registro
elseif ($uri === '/register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->showRegister();
} elseif ($uri === '/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->register($_POST);
}

// Manejo de rutas para dashboard
elseif ($uri === '/dashboard' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $tasks = $taskController->showTasks($_SESSION['user']);
    $notifications = $notificationController->showNotifications($_SESSION['user']);
    include __DIR__ . '/../app/Views/dashboard.php'; 
}

// Manejo de logout
elseif ($uri === '/logout' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->logout();
}

// Manejo de rutas para crear tarea
elseif ($uri === '/task/create' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $categories = $categoryModel->getCategories();
    include __DIR__ . '/../app/Views/create_task.php';
} elseif ($uri === '/task/create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskController->createTask($_POST);
}

// Manejo de rutas para editar tarea
elseif (preg_match('/^\/task\/edit\/(\d+)$/', $uri, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $taskId = $matches[1];
    $task = $taskController->getTaskById($taskId);
    $categories = $categoryModel->getCategories();
    include __DIR__ . '/../app/Views/edit_task.php';
} elseif (preg_match('/^\/task\/edit\/(\d+)$/', $uri, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskController->editTask($matches[1], $_POST);
}

// Manejo de rutas para eliminar tarea
elseif (preg_match('/^\/task\/delete\/(\d+)$/', $uri, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $taskController->deleteTask($matches[1]);
}

// Manejo de rutas para crear notificación
elseif ($uri === '/notification/create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Asegurarse de que $_SESSION['user'] y $_POST['message'] están definidos
    $userId = $_SESSION['user'] ?? null;
    $message = $_POST['message'] ?? null;

    if ($userId && $message) {
        $notificationController->createNotification($userId, $message);
    } else {
        $_SESSION['error'] = "Faltan datos para crear la notificación.";
        header('Location: /dashboard');
        exit;
    }
}

// Manejo de rutas para marcar notificación como leída
elseif (preg_match('/^\/notification\/mark-read\/(\d+)$/', $uri, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $notificationId = $matches[1];
    $notificationController->markAsRead($notificationId);  // Método correcto
}

// Manejo de rutas para eliminar notificación
elseif (preg_match('/^\/notification\/delete\/(\d+)$/', $uri, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $notificationId = $matches[1];
    $notificationController->delete($notificationId);  // Método para eliminar la notificación
}

// Si no se encuentra la ruta, mostrar 404
else {
    echo "404 Not Found";
}
