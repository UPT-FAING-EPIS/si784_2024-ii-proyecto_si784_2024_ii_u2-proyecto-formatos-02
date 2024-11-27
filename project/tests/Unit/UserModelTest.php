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

    /**
     * Verifica que la contraseña del usuario pueda ser verificada correctamente.
     */
    public function testPasswordVerification()
    {
        $testEmail = "userverification@gmail.com";
        $testPassword = password_hash("securepassword", PASSWORD_BCRYPT);

        $this->userModel->createUser([
            'name' => 'Verification User',
            'email' => $testEmail,
            'password' => $testPassword,
        ]);

        $user = $this->userModel->findUserByEmail($testEmail);
        $this->assertNotNull($user, "El usuario no fue encontrado.");

        $isPasswordCorrect = password_verify("securepassword", $user['password']);
        $this->assertTrue($isPasswordCorrect, "La contraseña no coincide.");
    }

    /**
     * Verifica que no se pueda crear un usuario con un correo duplicado.
     */
    public function testCreateUserWithDuplicateEmail()
    {
        // Correo único para evitar duplicados
        $testEmail = "duplicateemail_" . uniqid() . "@gmail.com";
        $testPassword = password_hash("password123", PASSWORD_BCRYPT);

        // Crear el primer usuario con un correo único
        $this->userModel->createUser([
            'name' => 'User One',
            'email' => $testEmail,
            'password' => $testPassword,
        ]);

        // Intentar crear un segundo usuario con el mismo correo
        $result = $this->userModel->createUser([
            'name' => 'User Two',
            'email' => $testEmail,
            'password' => $testPassword,
        ]);

        $this->assertFalse($result, "Se creó un usuario con un correo duplicado.");
    }
}