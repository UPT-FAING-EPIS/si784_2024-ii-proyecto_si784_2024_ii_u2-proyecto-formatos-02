<?php
  require_once 'models/producto.php';

  class ProductoController
  {
    public function index()
    {
      $producto = new Producto();
      $productos = $producto->productoRandom(6);

      require_once 'views/productos/destacados.php';
    }

    public function ver()
    {
      if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $producto = new Producto();
        $producto->setId($id);

        $pro = $producto->obtenerUno();

        require_once 'views/productos/ver.php';
      }
    }

    public function gestion()
    {
      Util::esAdmin();
      $producto = new Producto();
      $productos = $producto->obtenerTodo();

      require_once 'views/productos/gestion.php';
    }

    public function crear() 
    {
      util::esAdmin();

      require_once 'views/productos/crear.php';
    }

    public function guardar()
    {
      Util::esAdmin();
      
      if (isset($_POST)) {
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : false;
        $precio = isset($_POST['precio']) ? $_POST['precio'] : false;
        $stock = isset($_POST['stock']) ? $_POST['stock'] : false;
        $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : false;
        // $imagen = isset($_POST['nombre']) ? $_POST['nombre'] : false;

        if ($nombre && $descripcion && $precio && $stock && $categoria) {
          $producto = new Producto();
          $producto->setNombre($nombre);
          $producto->setDescripcion($descripcion);
          $producto->setPrecio($precio);
          $producto->setStock($stock);
          $producto->setCategoria_id($categoria);

          if (isset($_FILES['imagen'])) {
            $archivo = $_FILES['imagen'];
            $archivo_nombre = $archivo['name'];
            $tipo_archivo = $archivo['type'];
            
            if ($tipo_archivo == 'image/jpg' || $tipo_archivo == 'image/jpeg' || $tipo_archivo == 'image/png' || $tipo_archivo == 'image/gif') {
              
              if (!is_dir('subidas/imagenes')) {
                mkdir('subidas/imagenes', 0777, true);
              }
              move_uploaded_file($archivo['tmp_name'], 'subidas/imagenes/' . $archivo_nombre);
              $producto->setImagen($archivo_nombre);
            }
          }

          if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $producto->setId($id);
            $guardar = $producto->editar();
          } else {
            $guardar = $producto->guardar();
          }

          if ($guardar) {
            $_SESSION['producto'] = 'completado';
          } else {
            $_SESSION['producto'] = 'falla';
          }
        } else {
          $_SESSION['producto'] = 'falla';
        }
      } else {
        $_SESSION['producto'] = 'falla';
      }

      header('Location:' . URL_BASE . 'Producto/gestion');
    }

    public function editar()
    {
      Util::esAdmin();
      
      if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $editar = true;
        $producto = new Producto();
        $producto->setId($id);

        $pro = $producto->obtenerUno();

        require_once 'views/productos/crear.php';
      } else {
        header('Location:' . URL_BASE . 'Producto/gestion');
      }
    }

    public function eliminar()
    {
      Util::esAdmin();
      
      if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $producto = new Producto();
        $producto->setId($id);
        
        $borrar = $producto->eliminar();

        if ($borrar) {
          $_SESSION['borrar'] = 'completado';
        } else {
          $_SESSION['borra'] = 'falla';
        }
      } else {
        $_SESSION['borra'] = 'falla';
      }

      header('Location:' . URL_BASE . 'Producto/gestion');
    }
  }
?>