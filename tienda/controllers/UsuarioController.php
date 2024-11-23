<?php
  require_once 'models/usuario.php';

  class UsuarioController
  {
    public function registro()
    {
      require_once 'views/usuarios/registro.php';
    }

    public function guardar()
    {
      if (isset($_POST)) {
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
        $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : false;
        $email = isset($_POST['email']) ? $_POST['email'] : false;
        $password = isset($_POST['password']) ? $_POST['password'] : false;

        if ($nombre && $apellidos && $email && $password) {
          $usuario = new Usuario();
          $usuario->setNombre($nombre);
          $usuario->setApellidos($apellidos);
          $usuario->setEmail($email);
          $usuario->setPassword($password);

          $guardar = $usuario->guardarBase();

          if ($guardar) {
            $_SESSION['registro'] = 'completado';
          } else {
            $_SESSION['registro'] = 'falla';
          }
        } else {
          $_SESSION['registro'] = 'falla';
        }
      } else {
        $_SESSION['registro'] = 'falla';
      }
      header('Location:' . URL_BASE . 'Usuario/registro');
    }

    public function login()
    {
      if (isset($_POST)) {
        $usuario = new Usuario();
        $usuario->setEmail($_POST['email']);
        $usuario->setPassword($_POST['password']);

        $identificacion = $usuario->login();

        if ($identificacion && is_object($identificacion)) {
          $_SESSION['identidad'] = $identificacion;

          if ($identificacion->rol == 'admin') {
            $_SESSION['admin'] = true;
          }
        } else {
          $_SESSION['error_login'] = 'Identificación fallida';
        }

      }
      header('Location:' . URL_BASE);
    }

    public function logout()
    {
      if (isset($_SESSION['identidad'])) {
        $_SESSION['identidad'] = null;
        unset($_SESSION['identidad']);
      }

      if (isset($_SESSION['admin'])) {
        $_SESSION['admin'] = null;
        unset($_SESSION['admin']);
      }

      header('Location:' . URL_BASE);
    }
  }
?>