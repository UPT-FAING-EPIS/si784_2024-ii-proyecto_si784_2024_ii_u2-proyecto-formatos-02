<?php
// NotificationModel.php

namespace App\Models;

class NotificationModel extends Database
{
    public $conn;
    // Crear notificación
    public function createNotification($data)
    {
        if (isset($data['user_id'], $data['message'])) {
            $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
            return $stmt->execute($data);
        } else {
            // Si falta algún campo, manejar el error
            return false;
        }
    }

    // Obtener notificaciones de un usuario
    public function getNotificationsByUser($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Marcar notificación como leída
    public function markAsRead($id)
    {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Eliminar notificación
    public function deleteNotification($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

}
