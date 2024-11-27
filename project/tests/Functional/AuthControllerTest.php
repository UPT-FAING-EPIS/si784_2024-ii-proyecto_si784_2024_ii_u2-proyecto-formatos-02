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



}