<?php
namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController
{
    protected $notificationModel;
    public $redirect; // Propiedad para almacenar redirecciones simuladas

    public function __construct($notificationModel)

    {
    
        $this->notificationModel = $notificationModel;
    
    }

    // Método centralizado para redirecciones
    protected function redirectTo($url)
    {
        if (getenv('APP_ENV') === 'testing') {
            // En modo pruebas, almacena la redirección
            $this->redirect = $url;
        } else {
            // En producción, realiza la redirección real
            header("Location: $url");
            exit;
        }
    }

    // Crear una notificación
    public function createNotification($userId, $message)
    {
        if (!$userId) {
            $this->redirectTo('/login');
            return false;
        }

        if (empty($message)) {
            $_SESSION['error'] = "El mensaje no puede estar vacío.";
            $this->redirectTo('/dashboard');
            return false;
        }

        $data = [
            'user_id' => $userId,
            'message' => $message
        ];

        if ($this->notificationModel->createNotification($data)) {
            $_SESSION['success'] = 'Notificación creada con éxito.';
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = "Error al crear la notificación.";
            $this->redirectTo('/dashboard');
            return false;
        }
    }

    // Marcar una notificación como leída
    public function markAsRead($id)
    {
        if ($this->notificationModel->markAsRead($id)) {
            $_SESSION['success'] = "Notificación marcada como leída.";
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = "Error al marcar la notificación como leída.";
            $this->redirectTo('/dashboard');
            return false;
        }
    }

    // Eliminar una notificación
    public function delete($id)
    {
        if ($this->notificationModel->deleteNotification($id)) {
            $_SESSION['success'] = "Notificación eliminada.";
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = "Error al eliminar la notificación.";
            $this->redirectTo('/dashboard');
            return false;
        }
    }

    // Mostrar las notificaciones del usuario
    public function showNotifications($userId)
    {
        if (!$userId) {
            $this->redirectTo('/login');
            return false; // Asegúrate de devolver false en este caso
        }
    
        return $this->notificationModel->getNotificationsByUser($userId);
    }
}
