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
     * Verifica que el inicio de sesión falle si se proporcionan credenciales incorrectas.
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

    /**
     * Verifica que un usuario puede registrarse correctamente con datos válidos.
     */
    public function testRegisterWithValidData()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Simular que el correo no existe
        $this->mockUserModel->shouldReceive('findUserByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn(null);

        // Simular creación exitosa del usuario
        $this->mockUserModel->shouldReceive('createUser')
            ->once()
            ->with(m::on(function ($input) use ($data) {
                return $input['name'] === $data['name'] &&
                       $input['email'] === $data['email'] &&
                       password_verify($data['password'], $input['password']);
            }))
            ->andReturn(true);

        // Ejecutar el método
        $result = $this->authController->register($data);

        // Verificar que el registro fue exitoso
        $this->assertTrue($result);
        $this->assertEquals("Usuario registrado con éxito. Por favor, inicia sesión.", $_SESSION['success']);
        $this->assertEquals('/login', $this->authController->redirect);
    }

    /**
     * Verifica que el registro falle si el correo ya está registrado.
     */
    public function testRegisterWithExistingEmail()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Simular que el correo ya existe
        $this->mockUserModel->shouldReceive('findUserByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn(['id' => 1]);

        // Ejecutar el método
        $result = $this->authController->register($data);

        // Verificar que el registro falló
        $this->assertFalse($result);
        $this->assertEquals("El correo ya está registrado.", $_SESSION['error']);
        $this->assertEquals('/register', $this->authController->redirect);
    }

    /**
     * Verifica que el registro falle si faltan campos obligatorios.
     */
    public function testRegisterWithMissingFields()
    {
        $data = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        $result = $this->authController->register($data);

        $this->assertFalse($result);
        $this->assertEquals("Por favor, complete todos los campos.", $_SESSION['error']);
        $this->assertEquals('/register', $this->authController->redirect);
    }

    /**
     * Verifica el manejo del error cuando la creación del usuario falla en el modelo.
     */
    public function testRegisterFailsToCreateUser()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $this->mockUserModel->shouldReceive('findUserByEmail')
            ->once()
            ->with($data['email'])
            ->andReturn(null);

        $this->mockUserModel->shouldReceive('createUser')
            ->once()
            ->andReturn(false);

        $result = $this->authController->register($data);

        $this->assertFalse($result);
        $this->assertEquals("Error al registrar al usuario.", $_SESSION['error']);
        $this->assertEquals('/register', $this->authController->redirect);
    }

    /**
     * Verifica que el método logout destruya correctamente la sesión.
     */
    public function testLogout()
    {
        // Simular sesión activa
        $_SESSION['user'] = 1;

        // Ejecutar el método
        $result = $this->authController->logout();

        // Verificar que la sesión fue destruida
        $this->assertTrue($result);
        $this->assertEquals('/login', $this->authController->redirect);
    }

    /**
     * Verifica que el logout funcione incluso cuando no hay una sesión activa.
     */
    public function testLogoutWithoutActiveSession()
    {
        unset($_SESSION['user']);

        $result = $this->authController->logout();

        $this->assertTrue($result);
        $this->assertEquals('/login', $this->authController->redirect);
        $this->assertEquals("Has cerrado sesión exitosamente.", $_SESSION['success']);
    }

    // PRUEBAS DE INTEGRACIÓN
    /**
     * Verifica que un usuario autenticado sea redirigido al dashboard al acceder al login.
     */
    public function testShowLoginRedirectsIfAuthenticated()
    {
        $_SESSION['user'] = 1;

        // Ejecutar el método
        $this->authController->showLogin();

        // Verificar redirección
        $this->assertEquals('/dashboard', $this->authController->redirect);
    }

    /**
     * Verifica que la vista de login sea generada correctamente para un usuario no autenticado.
     */
    public function testShowLoginIncludesLoginView()
    {
        unset($_SESSION['user']);

        // Capturar la salida generada por el método
        ob_start();
        $this->authController->showLogin();
        $output = ob_get_clean();

        // Verificar contenido relevante en la vista de login
        $this->assertStringContainsString('<h1>Login</h1>', $output, 'La vista de login no contiene el título esperado.');
        $this->assertStringContainsString('<form method="POST" action="/login">', $output, 'La vista de login no contiene el formulario esperado.');
    }

    /**
     * Verifica que un usuario autenticado sea redirigido al dashboard al intentar registrarse.
     */
    public function testShowRegisterRedirectsIfAuthenticated()
    {
        $_SESSION['user'] = 1;

        $this->authController->showRegister();

        $this->assertEquals('/dashboard', $this->authController->redirect);
    }

    /**
     * Verifica que la vista de registro sea generada correctamente para un usuario no autenticado.
     */
    public function testShowRegisterIncludesRegisterView()
    {
        unset($_SESSION['user']);

        ob_start();
        $this->authController->showRegister();
        $output = ob_get_clean();

        $this->assertStringContainsString('<h1>Registro</h1>', $output, 'La vista de registro no contiene el título esperado.');
        $this->assertStringContainsString('<form method="POST" action="/register">', $output, 'La vista de registro no contiene el formulario esperado.');
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
