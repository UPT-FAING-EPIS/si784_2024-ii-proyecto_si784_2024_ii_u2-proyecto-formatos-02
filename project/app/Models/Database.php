<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    protected $conn;

    public function __construct()
    {
        $config = include __DIR__ . '/../../config/database.php';
    
        if (empty($config['host']) || empty($config['db']) || empty($config['user']) || !isset($config['pass'])) {
            throw new \InvalidArgumentException("Invalid database configuration.");
        }
    
        try {
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['db']}",
                $config['user'],
                $config['pass']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
    
    // Método para acceder a la conexión
    public function getConnection()
    {
        return $this->conn;
    }
}
