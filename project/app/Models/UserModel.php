<?php

namespace App\Models;

class UserModel extends Database
{
    public function findUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        // Si no se encuentra ningún usuario, devolver null
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user === false ? null : $user;
    }
    

    public function createUser($data)
    {
        // Verificar si los campos obligatorios están presentes
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return false; // Retornar false si falta algún campo
        }
    
        // Verificar si el correo electrónico ya existe
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $data['email']]);
        $emailExists = $stmt->fetchColumn();
    
        if ($emailExists > 0) {
            // Si el correo ya existe, retornamos false
            return false;
        }
    
        // Preparar la consulta para insertar el nuevo usuario
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    
        // Ejecutar la consulta con los datos
        return $stmt->execute($data);
    }
    
    
}
