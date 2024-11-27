<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Models\NotificationModel;

class NotificationModelTest extends TestCase
{
    protected $mockConn;
    protected $notificationModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConn = m::mock('PDO'); // Crear un mock para la conexión PDO
        $this->notificationModel = new NotificationModel(); // Instanciar el modelo NotificationModel
        $this->notificationModel->conn = $this->mockConn; // Asignar la conexión mockeada al modelo
    }

    /**
     * Verifica que se pueda crear una notificación exitosamente con datos válidos.
     */
    public function testCreateNotificationSuccess()
    {
        $data = [
            'user_id' => 1,
            'message' => 'Test notification',
        ];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with($data)
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)")
             ->andReturn($stmt);

        $result = $this->notificationModel->createNotification($data);

        $this->assertTrue($result);
    }

    /**
     * Verifica que falle la creación de una notificación si faltan campos requeridos.
     */
    public function testCreateNotificationFailsWithoutRequiredFields()
    {
        $data = ['message' => 'Missing user_id'];

        $result = $this->notificationModel->createNotification($data);

        $this->assertFalse($result);
    }

    /**
     * Verifica que se puedan obtener las notificaciones asociadas a un usuario exitosamente.
     */
    public function testGetNotificationsByUserSuccess()
    {
        $userId = 1;
        $expected = [
            ['id' => 1, 'user_id' => 1, 'message' => 'Test notification', 'is_read' => 0],
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
             ->with("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC")
             ->andReturn($stmt);

        $result = $this->notificationModel->getNotificationsByUser($userId);

        $this->assertEquals($expected, $result);
    }

    /**
     * Verifica que se pueda marcar una notificación como leída exitosamente.
     */
    public function testMarkAsReadSuccess()
    {
        $id = 1;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with(['id' => $id])
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("UPDATE notifications SET is_read = 1 WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->notificationModel->markAsRead($id);

        $this->assertTrue($result);
    }

    /**
     * Verifica que se pueda eliminar una notificación exitosamente por su ID.
     */
    public function testDeleteNotificationSuccess()
    {
        $id = 1;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with(['id' => $id])
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("DELETE FROM notifications WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->notificationModel->deleteNotification($id);

        $this->assertTrue($result);
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
