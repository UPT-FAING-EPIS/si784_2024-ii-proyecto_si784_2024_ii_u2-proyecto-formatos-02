<?php
  require_once 'models/producto.php';

  class CarritoController
  {
    public function index()
    {
      if (isset($_SESSION['carrito'])) {
        $carrito = $_SESSION['carrito'];
      } else {
        $carrito = array();
      }

      require_once 'views/carrito/index.php';
    }

    public function agregar()
    {
      if (isset($_GET['id'])) {
        $pro_id = $_GET['id'];
      } else {
        header('Location:' . URL_BASE);
      }

      if (isset($_SESSION['carrito'])) {
        $cont = 0;
        foreach ($_SESSION['carrito'] as $key => $value) {
          if ($value['id_producto'] == $pro_id) {
            $_SESSION['carrito'][$key]['unidades']++;
            $cont++;
          }
        }
      }

      if (!isset($cont) || $cont == 0) {
        $producto = new Producto();
        $producto->setId($pro_id);
        $producto = $producto->obtenerUno();
  
        if (is_object($producto)) {
          $_SESSION['carrito'][] = array(
            'id_producto' => $producto->id,
            'precio' => $producto->precio,
            'unidades' => 1,
            'producto' => $producto
          );
        }
      }

      header('Location:' . URL_BASE . 'Carrito/index');  
    }

    public function remover()
    {
      if(isset($_GET['index'])){
        $index = $_GET['index'];
        unset($_SESSION['carrito'][$index]);
      }

      header('Location:' . URL_BASE . 'Carrito/index');
    }

    public function mas()
    {
      if(isset($_GET['index'])){
        $index = $_GET['index'];
        $_SESSION['carrito'][$index]['unidades'] ++;
      }

      header('Location:' . URL_BASE . 'Carrito/index');
    }

    public function menos()
    {
      if(isset($_GET['index'])){
        $index = $_GET['index'];
        $_SESSION['carrito'][$index]['unidades'] --;
        if ($_SESSION['carrito'][$index]['unidades'] == 0) {
          unset($_SESSION['carrito'][$index]);
        }
      }

      header('Location:' . URL_BASE . 'Carrito/index');
    }

    public function eliminarTodo()
    {
      unset($_SESSION['carrito']);

      header('Location:' . URL_BASE . 'Carrito/index');  
    }
  }
