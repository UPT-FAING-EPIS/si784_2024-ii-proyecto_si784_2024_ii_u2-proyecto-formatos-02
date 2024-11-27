<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Models\TaskModel;

class TaskModelTest extends TestCase
{
    protected $mockConn;
    protected $taskModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConn = m::mock('PDO'); // Crear un mock de la conexión PDO
        $this->taskModel = new TaskModel(); // Instanciar el modelo TaskModel
        $this->taskModel->conn = $this->mockConn; // Asignar la conexión mockeada al modelo
    }

    /**
     * Verifica que se pueda crear una tarea exitosamente con datos válidos.
     */
    public function testCreateTaskSuccess()
    {
        $data = [
            'user_id' => 1,
            'category_id' => 2,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => '2024-12-01',
        ];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with($data)
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("INSERT INTO tasks (user_id, title, description, category_id, due_date) VALUES (:user_id, :title, :description, :category_id, :due_date)")
             ->andReturn($stmt);

        $result = $this->taskModel->createTask($data);
        $this->assertTrue($result);
    }

    /**
     * Verifica que falle la creación de una tarea si faltan datos requeridos.
     */
    public function testCreateTaskFails()
    {
        $data = [
            'user_id' => 1,
            'title' => 'Test Task',
        ]; // Faltan campos requeridos

        $result = $this->taskModel->createTask($data);
        $this->assertFalse($result);
    }

    /**
     * Verifica que se puedan obtener las tareas asociadas a un usuario exitosamente.
     */
    public function testGetTasksByUserSuccess()
    {
        $userId = 1;
        $expected = [
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'category_name' => 'Category 1',
                'due_date' => '2024-12-01',
            ],
        ];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with(['user_id' => $userId])
             ->andReturn(true);
        $stmt->shouldReceive('fetchAll')
             ->once()
             ->with(\PDO::FETCH_ASSOC)
             ->andReturn($expected);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("SELECT tasks.*, categories.name AS category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.user_id = :user_id")
             ->andReturn($stmt);

        $result = $this->taskModel->getTasksByUser($userId);

        $this->assertEquals($expected, $result);
    }

    /**
     * Verifica que se pueda actualizar una tarea exitosamente con datos válidos.
     */
    public function testUpdateTaskSuccess()
    {
        $data = [
            'id' => 1,
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'category_id' => 2,
            'due_date' => '2024-12-02',
            'is_completed' => 1,
        ];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindParam')->with(':title', $data['title'])->once();
        $stmt->shouldReceive('bindParam')->with(':description', $data['description'])->once();
        $stmt->shouldReceive('bindParam')->with(':category_id', $data['category_id'])->once();
        $stmt->shouldReceive('bindParam')->with(':due_date', $data['due_date'])->once();
        $stmt->shouldReceive('bindParam')->with(':is_completed', $data['is_completed'])->once();
        $stmt->shouldReceive('bindParam')->with(':id', $data['id'])->once();
        $stmt->shouldReceive('execute')->once()->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("UPDATE tasks SET title = :title, description = :description, category_id = :category_id, due_date = :due_date, is_completed = :is_completed WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->taskModel->updateTask($data);
        $this->assertTrue($result);
    }

    /**
     * Verifica que falle la actualización de una tarea si faltan campos requeridos.
     */
    public function testUpdateTaskFailsWithoutRequiredFields()
    {
        $data = [
            'id' => 1,
            'description' => 'Missing title',
        ]; // Faltan campos requeridos

        $result = $this->taskModel->updateTask($data);
        $this->assertFalse($result);
    }

    /**
     * Verifica que se pueda eliminar una tarea exitosamente por su ID.
     */
    public function testDeleteTaskSuccess()
    {
        $taskId = 1;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with(['id' => $taskId])
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("DELETE FROM tasks WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->taskModel->deleteTask($taskId);
        $this->assertTrue($result);
    }

    /**
     * Verifica que se puedan obtener los detalles de una tarea por su ID exitosamente.
     */
    public function testGetTaskByIdSuccess()
    {
        $taskId = 1;
        $expected = [
            'title' => 'Task 1',
            'description' => 'Description 1',
            'category_name' => 'Category 1',
            'due_date' => '2024-12-01',
        ];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindParam')->once()->with(':id', $taskId);
        $stmt->shouldReceive('execute')->once()->andReturn(true);
        $stmt->shouldReceive('fetch')->once()->andReturn($expected);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("SELECT tasks.*, categories.name AS category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.id = :id")
             ->andReturn($stmt);

        $result = $this->taskModel->getTaskById($taskId);
        $this->assertEquals($expected, $result);
    }

    /**
     * Verifica que retorne null si no se encuentra una tarea con el ID dado.
     */
    public function testGetTaskByIdReturnsNullIfNotFound()
    {
        $taskId = 999; // ID no existente

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindParam')->once()->with(':id', $taskId);
        $stmt->shouldReceive('execute')->once()->andReturn(true);
        $stmt->shouldReceive('fetch')->once()->andReturn(null);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("SELECT tasks.*, categories.name AS category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.id = :id")
             ->andReturn($stmt);

        $result = $this->taskModel->getTaskById($taskId);
        $this->assertNull($result);
    }

    /**
     * Cierra los mocks después de cada prueba.
     */
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
