<?php
namespace App\Models;

class TaskModel extends Database
{
    public $conn;

    public function createTask($data)
    {
        if (isset($data['user_id'], $data['title'], $data['description'], $data['category_id'], $data['due_date'])) {
            $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, description, category_id, due_date) VALUES (:user_id, :title, :description, :category_id, :due_date)");
            return $stmt->execute($data);
        }
        return false;
    }

    public function getTasksByUser($userId)
    {
        $stmt = $this->conn->prepare("SELECT tasks.*, categories.name AS category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateTask($data)
    {
        if (!isset($data['id'], $data['title'], $data['category_id'])) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE tasks SET title = :title, description = :description, category_id = :category_id, due_date = :due_date, is_completed = :is_completed WHERE id = :id");
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':is_completed', $data['is_completed']);
        $stmt->bindParam(':id', $data['id']);
        return $stmt->execute();
    }

    public function deleteTask($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getTaskById($id)
    {
        $stmt = $this->conn->prepare("SELECT tasks.*, categories.name AS category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id WHERE tasks.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
