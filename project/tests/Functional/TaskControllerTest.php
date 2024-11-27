<?php
namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Controllers\TaskController;
use App\Models\TaskModel;
use App\Models\CategoryModel;

class TaskControllerTest extends TestCase
{
    protected $taskController;
    protected $mockTaskModel;
    protected $mockCategoryModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks para TaskModel y CategoryModel
        $this->mockTaskModel = m::mock(TaskModel::class);
        $this->mockCategoryModel = m::mock(CategoryModel::class);

        // Instanciar el TaskController con los mocks
        $this->taskController = new TaskController($this->mockTaskModel, $this->mockCategoryModel);
    }
    // PRUEBAS UNITARIAS
    /**
     * Verifica que las tareas asociadas a un usuario se devuelvan correctamente.
     */
    public function testShowTasks()
    {
        $userId = 1;

        // Datos de ejemplo para las tareas
        $tasks = [
            ['title' => 'aaa', 'description' => 'Test Description', 'user_id' => 1, 'category' => 2, 'is_completed' => 0, 'due_date' => '2024-12-01'],
            ['title' => 'a', 'description' => 'a', 'user_id' => 1, 'category' => 1, 'is_completed' => 0, 'due_date' => '2024-11-28']
        ];

        // Mockear el método `getTasksByUser` en TaskModel
        $this->mockTaskModel->shouldReceive('getTasksByUser')
            ->with($userId)
            ->andReturn($tasks)
            ->once();

        // Ejecutar el método del controlador
        $result = $this->taskController->showTasks($userId);

        // Verificar que el número de tareas devueltas sea correcto
        $this->assertCount(2, $result);

        // Verificar que los datos devueltos coincidan
        foreach ($tasks as $i => $task) {
            $this->assertEquals($task, $result[$i]);
        }
    }

    /**
     * Verifica que se pueda crear una tarea con datos válidos.
     */
    public function testCreateTaskWithValidData()
    {
        $_SESSION['user'] = 1; // Simular usuario autenticado
        $data = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'category_id' => 2,
            'due_date' => '2024-12-10',
        ];

        // Mockear el método createTask en TaskModel
        $this->mockTaskModel->shouldReceive('createTask')
            ->once()
            ->with(m::on(function ($input) use ($data) {
                return $input['title'] === $data['title'] &&
                    $input['description'] === $data['description'] &&
                    $input['category_id'] === $data['category_id'] &&
                    $input['user_id'] === $_SESSION['user'] &&
                    $input['due_date'] === $data['due_date'];
            }))
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->taskController->createTask($data);

        // Verificar que la tarea fue creada correctamente
        $this->assertTrue($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Tarea creada con éxito.', $_SESSION['success']);
    }

    /**
     * Verifica que falle la creación de una tarea si falta el campo `category_id`.
     */
    public function testCreateTaskWithMissingCategoryId()
    {
        $_SESSION['user'] = 1; // Simular usuario autenticado
        $data = [
            'title' => 'New Task',
            'description' => 'Task Description',
        ];

        // Ejecutar el método
        $result = $this->taskController->createTask($data);

        // Verificar que el método devuelve false al faltar datos
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Faltan datos: category_id', $_SESSION['error']);
    }

    /**
     * Verifica que se pueda editar una tarea con datos válidos.
     */
    public function testEditTaskWithValidData()
    {
        $taskId = 1;
        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'category_id' => 3,
            'due_date' => '2024-12-15',
        ];

        // Mockear el método updateTask en TaskModel
        $this->mockTaskModel->shouldReceive('updateTask')
            ->once()
            ->with(m::on(function ($input) use ($taskId, $data) {
                return $input['id'] === $taskId &&
                    $input['title'] === $data['title'] &&
                    $input['description'] === $data['description'] &&
                    $input['category_id'] === $data['category_id'] &&
                    $input['due_date'] === $data['due_date'];
            }))
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->taskController->editTask($taskId, $data);

        // Verificar que la tarea fue actualizada correctamente
        $this->assertTrue($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Tarea actualizada con éxito.', $_SESSION['success']);
    }

    /**
     * Verifica que una tarea sea eliminada correctamente.
     */
    public function testDeleteTaskSuccessfully()
    {
        $taskId = 1;

        // Mockear el método deleteTask en TaskModel
        $this->mockTaskModel->shouldReceive('deleteTask')
            ->once()
            ->with($taskId)
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->taskController->deleteTask($taskId);

        // Verificar que la tarea fue eliminada
        $this->assertTrue($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Tarea eliminada con éxito.', $_SESSION['success']);
    }

    /**
     * Verifica que falle al intentar editar una tarea si faltan datos requeridos.
     */
    public function testEditTaskWithInvalidData()
    {
        $taskId = 1;
        $data = [
            'title' => '', // Título vacío
            'description' => 'Updated Description',
            // category_id ausente
        ];

        // Ejecutar el método
        $result = $this->taskController->editTask($taskId, $data);

        // Verificar que el método devuelve false
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Faltan datos para actualizar la tarea.', $_SESSION['error']);
    }

    /**
     * Verifica que falle la eliminación de una tarea si ocurre un error en el modelo.
     */
    public function testDeleteTaskWithFailure()
    {
        $taskId = 1;

        // Mockear el método `deleteTask` en TaskModel para devolver false
        $this->mockTaskModel->shouldReceive('deleteTask')
            ->with($taskId)
            ->andReturn(false)
            ->once();

        // Ejecutar el método
        $result = $this->taskController->deleteTask($taskId);

        // Verificar que el método devuelve false
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Error al eliminar la tarea.', $_SESSION['error']);
    }

    /**
     * Simula una excepción al intentar crear una tarea para verificar el manejo de errores.
     */
    public function testCreateTaskWithException()
    {
        $_SESSION['user'] = 1; // Simular usuario autenticado
        $data = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'category_id' => 2,
            'due_date' => '2024-12-10',
        ];

        // Mockear el método createTask para lanzar una excepción
        $this->mockTaskModel->shouldReceive('createTask')
            ->once()
            ->andThrow(new \Exception('Database error'));

        // Ejecutar el método
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');
        $this->taskController->createTask($data);
    }

    /**
     * Simula una excepción al intentar eliminar una tarea para verificar el manejo de errores.
     */
    public function testDeleteTaskWithException()
    {
        $taskId = 1;

        // Mockear el método deleteTask para lanzar una excepción
        $this->mockTaskModel->shouldReceive('deleteTask')
            ->once()
            ->with($taskId)
            ->andThrow(new \Exception('Delete error'));

        // Ejecutar el método
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Delete error');
        $this->taskController->deleteTask($taskId);
    }

    /**
     * Verifica que falle al intentar editar una tarea con una categoría inexistente.
     */
    public function testEditTaskWithInvalidCategory()
    {
        $taskId = 1;
        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'category_id' => 999, // Categoría no existente
            'due_date' => '2024-12-15',
        ];

        // Mockear el método updateTask en TaskModel para devolver false
        $this->mockTaskModel->shouldReceive('updateTask')
            ->once()
            ->with(m::on(function ($input) use ($taskId, $data) {
                return $input['id'] === $taskId && $input['category_id'] === $data['category_id'];
            }))
            ->andReturn(false);

        // Ejecutar el método
        $result = $this->taskController->editTask($taskId, $data);

        // Verificar que el método devuelve false
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Error al editar la tarea.', $_SESSION['error']);
    }

    /**
     * Verifica que falle al intentar crear una tarea con una categoría inexistente.
     */
    public function testCreateTaskWithNonExistentCategory()
    {
        $_SESSION['user'] = 1; // Simular usuario autenticado
        $data = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'category_id' => 999, // Categoría no existente
            'due_date' => '2024-12-10',
        ];

        // Mockear el método `createTask` para devolver false
        $this->mockTaskModel->shouldReceive('createTask')
            ->once()
            ->with(m::on(function ($input) use ($data) {
                return $input['category_id'] === $data['category_id'];
            }))
            ->andReturn(false);

        // Ejecutar el método
        $result = $this->taskController->createTask($data);

        // Verificar que el método devuelve false y muestra el mensaje correcto
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Error al crear la tarea.', $_SESSION['error']);
    }

    // PRUEBAS DE INTEGRACIÓN
    /**
     * Verifica que se muestre el formulario de creación de tareas con las categorías disponibles.
     */
    public function testShowCreateTaskForm()
    {
        $categories = [
            ['id' => 1, 'name' => 'Work'],
            ['id' => 2, 'name' => 'Personal'],
        ];

        // Mockear el método `getCategories` en CategoryModel
        $this->mockCategoryModel->shouldReceive('getCategories')
            ->once()
            ->andReturn($categories);

        // Capturar salida de la vista
        ob_start();
        $this->taskController->showCreateTaskForm();
        $output = ob_get_clean();

        // Verificar que las categorías están en la salida
        foreach ($categories as $category) {
            $this->assertStringContainsString($category['name'], $output);
        }
    }

    /**
     * Verifica que el formulario de edición de tareas muestre correctamente los datos de una tarea existente.
     */
    public function testShowEditTaskFormWithValidTask()
    {
        $task = [
            'id' => 1,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'category_id' => 2,
            'due_date' => '2024-12-01',
            'is_completed' => 0, // Agregar clave para evitar warnings
        ];
    
        $categories = [
            ['id' => 1, 'name' => 'Work'],
            ['id' => 2, 'name' => 'Personal'],
        ];
    
        // Mockear el método `getTaskById`
        $this->mockTaskModel->shouldReceive('getTaskById')
            ->with($task['id'])
            ->andReturn($task)
            ->once();
    
        // Mockear el método `getCategories`
        $this->mockCategoryModel->shouldReceive('getCategories')
            ->andReturn($categories)
            ->once();
    
        // Capturar salida de la vista
        ob_start();
        $this->taskController->showEditTaskForm($task['id']);
        $output = ob_get_clean();
    
        // Verificar que los datos de la tarea y las categorías están en la salida
        $this->assertStringContainsString($task['title'], $output);
        $this->assertStringContainsString($categories[0]['name'], $output);
        $this->assertStringContainsString($categories[1]['name'], $output);
    }
    

    /**
     * Verifica que se muestre un mensaje de error si el formulario de edición intenta cargar una tarea inexistente.
     */
    public function testShowEditTaskFormWithInvalidTask()
    {
        $taskId = 999;

        // Mockear el método `getTaskById` en TaskModel para devolver null
        $this->mockTaskModel->shouldReceive('getTaskById')
            ->with($taskId)
            ->andReturn(null)
            ->once();

        // Capturar salida de la vista
        ob_start();
        $this->taskController->showEditTaskForm($taskId);
        $output = ob_get_clean();

        // Verificar que se muestra el mensaje de error
        $this->assertStringContainsString('Tarea no encontrada.', $output);
    }

    /**
     * Verifica que se pueda crear una tarea con un título largo y que el sistema lo maneje correctamente.
     */
    public function testCreateTaskWithLongTitle()
    {
        $_SESSION['user'] = 1; // Simular usuario autenticado
        $data = [
            'title' => str_repeat('a', 256), // Título de 256 caracteres
            'description' => 'Task Description',
            'category_id' => 2,
            'due_date' => '2024-12-10',
        ];

        // Mockear el método createTask en TaskModel
        $this->mockTaskModel->shouldReceive('createTask')
            ->once()
            ->with(m::on(function ($input) use ($data) {
                return $input['title'] === $data['title'];
            }))
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->taskController->createTask($data);

        // Verificar que la tarea fue creada correctamente
        $this->assertTrue($result);
        $this->assertEquals('/dashboard', $this->taskController->redirect);
        $this->assertEquals('Tarea creada con éxito.', $_SESSION['success']);
    }


}
