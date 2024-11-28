<?php
namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\CategoryModel;

class TaskController
{
    protected $taskModel;
    protected $categoryModel;
    public $redirect; // Propiedad para almacenar redirecciones simuladas

    // Constructor con dependencia para los modelos
    public function __construct($taskModel, $categoryModel)

    {
    
        $this->taskModel = $taskModel;
    
        $this->categoryModel = $categoryModel;
    
    }

    // Método para redirigir (real o simulado)
    protected function redirectTo($url)
    {
        if (getenv('APP_ENV') === 'testing') {
            // En modo de pruebas, almacena la redirección
            $this->redirect = $url;
        } else {
            // En producción, ejecuta la redirección real
            header("Location: $url");
            exit;
        }
    }

    public function showTasks($userId)
    {
        // Obtener las tareas del usuario usando solo el ID
        $tasks = $this->taskModel->getTasksByUser($userId);
        return $tasks; // Devolver las tareas
    }

    public function createTask($data)
    {
        if (!isset($data['category_id'])) {
            $_SESSION['error'] = "Faltan datos: category_id"; // Mensaje de error si falta category_id
            $this->redirectTo('/dashboard');
            return false;
        }

        $data['user_id'] = $_SESSION['user']; // Asignar el ID del usuario directamente

        if ($this->taskModel->createTask($data)) {
            $_SESSION['success'] = 'Tarea creada con éxito.'; // Mensaje de éxito al crear tarea
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = 'Error al crear la tarea.'; // Mensaje de error si no se crea la tarea
            $this->redirectTo('/dashboard');
            return false;
        }
    }

    public function showCreateTaskForm()
    {
        $categories = $this->categoryModel->getCategories();
        include __DIR__ . '/../Views/create_task.php';
    }

    public function editTask($id, $data)
    {
        if (empty($data['title']) || empty($data['description']) || !isset($data['category_id'])) {
            $_SESSION['error'] = "Faltan datos para actualizar la tarea."; // Error si faltan datos
            $this->redirectTo('/dashboard');
            return false;
        }

        $data['id'] = $id;

        if ($this->taskModel->updateTask($data)) {
            $_SESSION['success'] = 'Tarea actualizada con éxito.'; // Mensaje de éxito
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = 'Error al editar la tarea.'; // Mensaje de error
            $this->redirectTo('/dashboard');
            return false;
        }
    }

    public function deleteTask($id)
    {
        if ($this->taskModel->deleteTask($id)) {
            $_SESSION['success'] = 'Tarea eliminada con éxito.'; // Mensaje de éxito al eliminar la tarea
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = 'Error al eliminar la tarea.'; // Mensaje de error si falla la eliminación
            $this->redirectTo('/dashboard');
            return false;
        }
    }

    public function getTaskById($id)
    {
        return $this->taskModel->getTaskById($id);
    }

    public function showEditTaskForm($id)
    {
        // Obtener la tarea por ID
        $task = $this->taskModel->getTaskById($id);
    
        // Si no se encuentra la tarea, muestra un mensaje de error y termina
        if (!$task) {
            echo "Tarea no encontrada.";
            return;
        }
    
        // Obtener las categorías (si es necesario en la vista)
        $categories = $this->categoryModel->getCategories();
    
        // Inicializar valores predeterminados para claves de tarea
        $task['is_completed'] = $task['is_completed'] ?? 0; // Valor predeterminado si no existe
    
        // Incluir la vista y pasar las variables necesarias
        include __DIR__ . '/../Views/edit_task.php';
    }
    
}
