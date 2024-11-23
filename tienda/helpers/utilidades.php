<?php
  class Util
  {
    public static function eliminarSession($nombre)
    {
      if (isset($_SESSION[$nombre])) {
        $_SESSION[$nombre] = null;
        unset($_SESSION[$nombre]);
      }

      return $nombre;
    }

    public static function esAdmin()
    {
      if (!isset($_SESSION['admin'])) {
        header('Location:' . URL_BASE);
      } else {
        return true;
      }
    }

    public static function login()
    {
      if (!isset($_SESSION['identidad'])) {
        header('Location:' . URL_BASE);
      } else {
        return true;
      }
    }

    public static function mostrarCategorias()
    {
      require_once 'models/categoria.php';
      
      $categoria = new Categoria();
      $categorias = $categoria->obtenerTodo();

      return $categorias;
    }

    public static function estadisticaCarrito()
    {
      $estadistica = array(
        'cont' => 0,
        'total' => 0
      );

      if (isset($_SESSION['carrito'])) {
        $estadistica['cont'] = count($_SESSION['carrito']);

        foreach ($_SESSION['carrito'] as $value) {
          $estadistica['total'] += $value['precio'] * $value['unidades'];
        }
      }

      return $estadistica;
    }

    public static function mostrarEstado($estado) 
    {
      $valor = '';
      switch ($estado) {
        case 'pendiente':
          $valor = 'Pendiente';
          break;
        case 'preparacion':
          $valor = 'En preparación';
          break;
        case 'preparado':
          $valor = 'Preparado para enviar';
          break;
        case 'enviado':
          $valor = 'Enviado';
          break;
        
        default:
          $valor = 'Pendiente';
          break;
      }

      return $valor;
    }
  }
?>