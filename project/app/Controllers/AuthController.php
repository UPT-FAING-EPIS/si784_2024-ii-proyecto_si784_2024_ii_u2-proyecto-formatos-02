<?php
namespace App\Controllers;

use App\Models\UserModel;

class AuthController
{
    protected $userModel;
    public $redirect; // Propiedad para almacenar redirecciones simuladas

    public function __construct()
    {
        $this->userModel = new UserModel();

        // Verificar si la sesión ya está activa antes de iniciarla
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Método para redirigir (real o simulado)
    protected function redirectTo($url)
    {
        if (getenv('APP_ENV') === 'testing') {
            // En modo de pruebas, almacena la redirección
            $this->redirect = $url;
        } else {
            // En producción, ejecuta la redirección real
            header("Location: $url");
            exit;
        }
    }

    // Mostrar formulario de inicio de sesión
    public function showLogin()
    {
        if (isset($_SESSION['user'])) {
            $this->redirectTo('/dashboard');
            return;
        }

        include __DIR__ . '/../Views/login.php';
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        if (isset($_SESSION['user'])) {
            $this->redirectTo('/dashboard');
            return;
        }

        include __DIR__ . '/../Views/register.php';
    }

    // Procesar inicio de sesión
    public function login($data)
    {
        if (empty($data['email']) || empty($data['password'])) {
            $_SESSION['error'] = "Por favor, complete todos los campos.";
            $this->redirectTo('/login');
            return false;
        }

        $user = $this->userModel->findUserByEmail($data['email']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $_SESSION['user'] = $user['id'];
            $_SESSION['success'] = "Bienvenido, has iniciado sesión con éxito.";
            $this->redirectTo('/dashboard');
            return true;
        } else {
            $_SESSION['error'] = "Credenciales inválidas.";
            $this->redirectTo('/login');
            return false;
        }
    }

    // Procesar registro de usuario
    public function register($data)
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $_SESSION['error'] = "Por favor, complete todos los campos.";
            $this->redirectTo('/register');
            return false;
        }

        $user = $this->userModel->findUserByEmail($data['email']);
        if ($user) {
            $_SESSION['error'] = "El correo ya está registrado.";
            $this->redirectTo('/register');
            return false;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        if ($this->userModel->createUser($data)) {
            $_SESSION['success'] = "Usuario registrado con éxito. Por favor, inicia sesión.";
            $this->redirectTo('/login');
            return true;
        } else {
            $_SESSION['error'] = "Error al registrar al usuario.";
            $this->redirectTo('/register');
            return false;
        }
    }

    // Cerrar sesión
    public function logout()
    {
        session_destroy();
        $_SESSION['success'] = "Has cerrado sesión exitosamente.";
        $this->redirectTo('/login');
        return true;
    }

    public function setUserModel($userModel)
    {
        $this->userModel = $userModel;
    }
}
