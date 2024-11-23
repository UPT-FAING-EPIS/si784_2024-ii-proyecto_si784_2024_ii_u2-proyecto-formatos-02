<?php
  require_once 'models/pedido.php';

  class PedidoController 
  {
    public function hacer() 
    {
      require_once 'views/pedido/hacer.php';
    }

    public function agregar() 
    {
      if (isset($_SESSION['identidad'])) {
        $usuario_id = $_SESSION['identidad']->id;
        $provincia = isset($_POST['provincia']) ? $_POST['provincia'] : false;
        $localidad = isset($_POST['localidad']) ? $_POST['localidad'] : false;
        $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : false;

        $estad = Util::estadisticaCarrito();
        $coste = $estad['total'];

        if ($provincia && $localidad && $direccion) {
          $pedido = new Pedido();
          $pedido->setUsuario_id($usuario_id);
          $pedido->setProvincia($provincia);
          $pedido->setLocalidad($localidad);
          $pedido->setDireccion($direccion);
          $pedido->setCoste($coste);

          $guardar = $pedido->guardar();
          $guardar_linea = $pedido->guardar_linea();

          if ($guardar && $guardar_linea) {
            $_SESSION['pedido'] = 'Completo';
          } else {
            $_SESSION['pedido'] = 'Error';
          }

        } else {
          $_SESSION['pedido'] = 'Error';
        }
        
        header('Location:' . URL_BASE . 'Pedido/confirmado');

      } else {
        header('Location:' . URL_BASE);
      }
    }

    public function confirmado()
    {
      if (isset($_SESSION['identidad'])) {
        $identidad = $_SESSION['identidad'];
        $pedido = new Pedido();
        $pedido->setUsuario_id($identidad->id);

        $pedido = $pedido->obtenerUnoPorUsuario();

        $pedido_productos = new Pedido();
        $productos = $pedido_productos->productosPorPedido($pedido->id);
      }

      require_once 'views/pedido/confirmado.php';
    }

    public function misPedidos()
    {
      Util::login();

      $usuario_id = $_SESSION['identidad']->id;
      $pedido = new Pedido();
      $pedido->setUsuario_id($usuario_id);
      $pedidos = $pedido->obtenerTodoPorUsuario();

      require_once 'views/pedido/mis_pedidos.php';
    }

    public function detalle() 
    {
      util::login();

      if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $pedido = new Pedido();
        $pedido->setId($id);
        $pedido = $pedido->obtenerUno();

        $pedido_productos = new Pedido();
        $productos = $pedido_productos->productosPorPedido($id);

        require_once 'views/pedido/detalle.php';

      } else {

        header('Location:' . URL_BASE . 'Pedido/misPedidos');
      }
    }

    public function gestion()
    {
      Util::esAdmin();

      $gestion = true;
      $pedido = new Pedido();
      $pedidos = $pedido->obtenerTodo();

      require_once 'views/pedido/mis_pedidos.php';
    }

    public function estado()
    {
      Util::esAdmin();

      if (isset($_POST['pedido_id']) && isset($_POST['estado'])) {
        $id = $_POST['pedido_id'];
        $estado = $_POST['estado'];

        $pedido = new Pedido();
        $pedido->setId($id);
        $pedido->setEstado($estado);
        $pedido->actualizarUnPedido();

        header('Location:' . URL_BASE . 'Pedido/detalle&id=' . $id);

      } else {

        header('Location:' . URL_BASE);
      }
      
    }
  }
?>