<?php
namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Controllers\NotificationController;
use App\Models\NotificationModel;

class NotificationControllerTest extends TestCase
{
    protected $notificationController;
    protected $mockNotificationModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mock para NotificationModel
        $this->mockNotificationModel = m::mock(NotificationModel::class);

        // Instanciar NotificationController con el mock
        $this->notificationController = new NotificationController($this->mockNotificationModel);
    }

    // PRUEBAS UNITARIAS
    /**
     * Verifica que se pueda crear una notificación correctamente con datos válidos.
     */
    public function testCreateNotificationWithValidData()
    {
        $userId = 1;
        $message = "Test notification message";

        // Mockear el método createNotification para devolver true
        $this->mockNotificationModel->shouldReceive('createNotification')
            ->once()
            ->with([
                'user_id' => $userId,
                'message' => $message,
            ])
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->notificationController->createNotification($userId, $message);

        // Verificar que el método retorne true
        $this->assertTrue($result);

        // Verificar redirección simulada
        $this->assertEquals('/dashboard', $this->notificationController->redirect);

        // Verificar mensaje en la sesión
        $this->assertEquals('Notificación creada con éxito.', $_SESSION['success']);
    }

    /**
     * Verifica que no se pueda crear una notificación si el mensaje está vacío.
     */
    public function testCreateNotificationWithEmptyMessage()
    {
        $userId = 1;
        $message = "";

        // Ejecutar el método
        $result = $this->notificationController->createNotification($userId, $message);

        // Verificar que el método retorne false
        $this->assertFalse($result);

        // Verificar redirección simulada
        $this->assertEquals('/dashboard', $this->notificationController->redirect);

        // Verificar mensaje de error en la sesión
        $this->assertEquals("El mensaje no puede estar vacío.", $_SESSION['error']);
    }

    /**
     * Verifica que no se pueda crear una notificación si el usuario no está logueado.
     */
    public function testCreateNotificationWithoutUserSession()
    {
        $userId = null;
        $message = "Test notification message";

        // Ejecutar el método
        $result = $this->notificationController->createNotification($userId, $message);

        // Verificar que el método retorne false
        $this->assertFalse($result);

        // Verificar redirección simulada
        $this->assertEquals('/login', $this->notificationController->redirect);
    }

    /**
     * Verifica que el método marque correctamente una notificación como leída.
     */
    public function testMarkAsReadWithSuccess()
    {
        $notificationId = 1;

        // Mockear el método markAsRead para devolver true
        $this->mockNotificationModel->shouldReceive('markAsRead')
            ->once()
            ->with($notificationId)
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->notificationController->markAsRead($notificationId);

        // Verificar que el método retorne true
        $this->assertTrue($result);

        // Verificar redirección simulada
        $this->assertEquals('/dashboard', $this->notificationController->redirect);

        // Verificar mensaje en la sesión
        $this->assertEquals("Notificación marcada como leída.", $_SESSION['success']);
    }

    /**
     * Verifica que el método elimine correctamente una notificación existente.
     */
    public function testDeleteNotificationWithSuccess()
    {
        $notificationId = 1;

        // Mockear el método deleteNotification para devolver true
        $this->mockNotificationModel->shouldReceive('deleteNotification')
            ->once()
            ->with($notificationId)
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->notificationController->delete($notificationId);

        // Verificar que el método retorne true
        $this->assertTrue($result);

        // Verificar redirección simulada
        $this->assertEquals('/dashboard', $this->notificationController->redirect);

        // Verificar mensaje en la sesión
        $this->assertEquals("Notificación eliminada.", $_SESSION['success']);
    }

    // PRUEBAS DE INTEGRACIÓN
    /**
     * Verifica que el controlador gestione correctamente la creación de una notificación con un ID de usuario inválido.
     */
    public function testCreateNotificationWithInvalidUserId()
    {
        $userId = "invalid_user";
        $message = "Test notification message";

        // Mock para caso inválido
        $this->mockNotificationModel->shouldReceive('createNotification')
            ->once()
            ->with(m::on(function ($input) {
                return !is_numeric($input['user_id']);
            }))
            ->andReturn(false);

        // Ejecutar método
        $result = $this->notificationController->createNotification($userId, $message);

        // Verificar resultado
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->notificationController->redirect);
        $this->assertEquals("Error al crear la notificación.", $_SESSION['error']);
    }

    /**
     * Verifica que el controlador devuelva las notificaciones asociadas a un usuario.
     */
    public function testShowNotifications()
    {
        $userId = 1;

        // Mockear el método getNotificationsByUser para devolver una lista de notificaciones
        $notifications = [
            ['id' => 1, 'message' => 'Test Notification 1', 'user_id' => $userId],
            ['id' => 2, 'message' => 'Test Notification 2', 'user_id' => $userId],
        ];

        $this->mockNotificationModel->shouldReceive('getNotificationsByUser')
            ->once()
            ->with($userId)
            ->andReturn($notifications);

        // Ejecutar el método
        $result = $this->notificationController->showNotifications($userId);

        // Verificar que el resultado sea el esperado
        $this->assertEquals($notifications, $result);
    }

    /**
     * Verifica que no se puedan mostrar notificaciones si no hay un usuario logueado.
     */
    public function testShowNotificationsWithoutUserLoggedIn()
    {
        $userId = null;

        // Mock para caso sin usuario logueado
        $this->mockNotificationModel->shouldReceive('getNotificationsByUser')
            ->never();

        // Ejecutar método
        $result = $this->notificationController->showNotifications($userId);

        // Verificar que el método retorne false
        $this->assertFalse($result);
        $this->assertEquals('/login', $this->notificationController->redirect);
    }

    /**
     * Verifica que el método falle al eliminar una notificación sin un ID válido.
     */
    public function testDeleteNotificationWithoutId()
    {
        $notificationId = null;

        // Mock para caso sin ID
        $this->mockNotificationModel->shouldReceive('deleteNotification')
            ->once()
            ->with(null)
            ->andReturn(false);

        // Ejecutar método
        $result = $this->notificationController->delete($notificationId);

        // Verificar que el método retorne false
        $this->assertFalse($result);
        $this->assertEquals('/dashboard', $this->notificationController->redirect);
        $this->assertEquals("Error al eliminar la notificación.", $_SESSION['error']);
    }

    /**
     * Verifica que el método falle si se intenta marcar como leída una notificación inexistente.
     */
    public function testMarkAsReadWithFailure()
    {
        $notificationId = 1;

        // Mockear el método markAsRead para devolver false
        $this->mockNotificationModel->shouldReceive('markAsRead')
            ->once()
            ->with($notificationId)
            ->andReturn(false);

        // Ejecutar el método
        $result = $this->notificationController->markAsRead($notificationId);

        // Verificar que el método retorne false
        $this->assertFalse($result);

        // Verificar redirección simulada
        $this->assertEquals('/dashboard', $this->notificationController->redirect);

        // Verificar mensaje de error en la sesión
        $this->assertEquals("Error al marcar la notificación como leída.", $_SESSION['error']);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
