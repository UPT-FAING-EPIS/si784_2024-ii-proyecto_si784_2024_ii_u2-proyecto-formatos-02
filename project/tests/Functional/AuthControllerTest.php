<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Controllers\AuthController;
use App\Models\UserModel;

class AuthControllerTest extends TestCase
{
    protected $authController;
    protected $mockUserModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un mock para UserModel
        $this->mockUserModel = m::mock(UserModel::class);

        // Instanciar AuthController con el mock
        $this->authController = new AuthController();
        $this->authController->setUserModel($this->mockUserModel);
    }

    // PRUEBAS UNITARIAS
    /**
     * Verifica que un usuario puede iniciar sesión correctamente con credenciales válidas.
     */
    public function testLoginWithValidCredentials()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        // Simular usuario existente en la base de datos
        $this->mockUserModel->shouldReceive('findUserByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn([
                'id' => 1,
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);

        // Ejecutar el método
        $result = $this->authController->login($data);

        // Verificar que el inicio de sesión fue exitoso
        $this->assertTrue($result);
        $this->assertEquals(1, $_SESSION['user']);
        $this->assertEquals('/dashboard', $this->authController->redirect);
    }

    /**
     * AHORA Verifica que el inicio de sesión falle si se proporcionan credenciales incorrectas.
     */      
    public function testLoginWithInvalidCredentials()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        // Simular usuario existente con contraseña incorrecta
        $this->mockUserModel->shouldReceive('findUserByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn([
                'id' => 1,
                'password' => password_hash('correctpassword', PASSWORD_BCRYPT),
            ]);

        // Ejecutar el método
        $result = $this->authController->login($data);

        // Verificar que el inicio de sesión falló
        $this->assertFalse($result);
        $this->assertEquals("Credenciales inválidas.", $_SESSION['error']);
        $this->assertEquals('/login', $this->authController->redirect);
    }

    /**
     * Verifica que el método login gestione correctamente cuando faltan campos en los datos.
     */
    public function testLoginWithMissingFields()
    {
        $data = [
            'email' => '',
            'password' => ''
        ];

        $result = $this->authController->login($data);

        $this->assertFalse($result);
        $this->assertEquals("Por favor, complete todos los campos.", $_SESSION['error']);
        $this->assertEquals('/login', $this->authController->redirect);
    }




}