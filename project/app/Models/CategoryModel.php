<?php
namespace App\Models;

class CategoryModel extends Database
{
    public $conn;
    // Crear categoría
    public function createCategory($data)
    {
        // Verificar que los datos recibidos sean correctos
        if (isset($data['name'])) {
            $stmt = $this->conn->prepare("INSERT INTO categories (name) VALUES (:name)");
            return $stmt->execute($data);
        } else {
            // Si falta el campo 'name', manejar el error
            return false;
        }
    }

    // Obtener todas las categorías
    public function getCategories()
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Obtener categoría por ID
    public function getCategoryById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(); // Retorna la categoría encontrada o null si no existe
    }

    // Actualizar categoría
    public function updateCategory($data)
    {
        // Asegúrate de que el 'id' esté presente en el arreglo de datos
        if (!isset($data['id']) || !isset($data['name'])) {
            return false;  // Si falta el 'id' o el 'name', no actualices
        }

        // Preparar la consulta SQL para actualizar la categoría
        $query = "UPDATE categories SET name = :name WHERE id = :id";
        
        // Usar una declaración preparada
        $stmt = $this->conn->prepare($query);
        
        // Bind de los parámetros
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id', $data['id']);
        
        // Ejecutar la consulta
        return $stmt->execute();
    }

    // Eliminar categoría
    public function deleteCategory($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
