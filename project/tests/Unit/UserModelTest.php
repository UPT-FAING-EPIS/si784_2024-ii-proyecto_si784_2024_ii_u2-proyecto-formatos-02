<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\UserModel;

class UserModelTest extends TestCase
{
    protected $userModel;

    /**
     * Configura el modelo antes de cada prueba.
     */
    protected function setUp(): void
    {
        $this->userModel = new UserModel(); // Instancia del modelo de usuario
    }

    // Pruebas Unitarias

    /**
     * Verifica que un usuario pueda ser creado correctamente.
     */
    public function testCreateUser()
    {
        $testEmail = 'user' . uniqid() . '@gmail.com';
        $testPassword = password_hash('password123', PASSWORD_BCRYPT);

        // Crear usuario
        $result = $this->userModel->createUser([
            'name' => 'Test User',
            'email' => $testEmail,
            'password' => $testPassword,
        ]);

        // Verificar si el usuario se ha creado
        $this->assertTrue($result);
    }
    
}