<?php
  class Producto
  {
    private $id;
    private $categoria_id;
    private $nombre;
    private $descripcion;
    private $precio;
    private $stock;
    private $oferta;
    private $fecha;
    private $imagen;
    private $db;

    public function __construct()
    {
      $this->db = Database::conexion();
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCategoria_id()
    {
        return $this->categoria_id;
    }
    public function setCategoria_id($categoria_id)
    {
        $this->categoria_id = $categoria_id;

        return $this;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $this->db->real_escape_string($nombre);

        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $this->db->real_escape_string($descripcion);

        return $this;
    }

    public function getPrecio()
    {
      return $this->precio;
    }
    public function setPrecio($precio)
    {
      $this->precio = $this->db->real_escape_string($precio);
      
      return $this;
    }
    
    public function getStock()
    {
      return $this->stock;
    }
    public function setStock($stock)
    {
      $this->stock = $this->db->real_escape_string($stock);
      
      return $this;
    }
    
    public function getOferta()
    {
      return $this->oferta;
    }
    public function setOferta($oferta)
    {
      $this->oferta = $this->db->real_escape_string($oferta);
      
      return $this;
    }
    
    public function getFecha()
    {
      return $this->fecha;
    }
    public function setFecha($fecha)
    {
      $this->fecha = $fecha;
      
      return $this;
    }

    public function getImagen()
    {
        return $this->imagen;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
  
        return $this;
    }

    public function obtenerTodo()
    {
      $sql = "
        SELECT * FROM productos 
        ORDER BY id DESC;
      ";
      $producto = $this->db->query($sql);

      return $producto;
    }

    public function obtenerTodoCategoria()
    {
      $sql = "
        SELECT p.*, c.nombre AS 'catnombre'
        FROM productos p
        INNER JOIN categorias c ON c.id = p.categoria_id
        WHERE p.categoria_id = {$this->getCategoria_id()}
        ORDER BY id DESC; 
      ";
      $producto = $this->db->query($sql);

      return $producto;
    }

    public function obtenerUno()
    {
      $sql = "
        SELECT * FROM productos
        WHERE id = {$this->getId()};
      ";
      $producto = $this->db->query($sql);

      return $producto->fetch_object();
    }

    public function productoRandom($limite)
    {
      $sql = "
        SELECT * FROM productos
        ORDER BY RAND() 
        LIMIT $limite;
      ";
      $productos = $this->db->query($sql);

      return $productos;
    }

    public function guardar()
    {
      $sql = "
        INSERT INTO productos
        VALUES (null, '{$this->getCategoria_id()}', '{$this->getNombre()}', '{$this->getDescripcion()}', {$this->getPrecio()}, {$this->getStock()}, null, CURDATE(), '{$this->getImagen()}');
      ";
      
      $guardar = $this->db->query($sql);
      $resul = false;

      if ($guardar) {
        $resul = true;
      }

      return $resul;
    }

    public function eliminar()
    {
      $sql = "
        DELETE FROM productos
        WHERE id = {$this->id};
      ";
      $borrar = $this->db->query($sql);
      $resul = false;

      if ($borrar) {
        $resul = true;
      }

      return $resul;
    }

    public function editar()
    {
      $sql = "
        UPDATE productos 
        SET categoria_id = {$this->getCategoria_id()}, nombre = '{$this->getNombre()}', descripcion = '{$this->getDescripcion()}', precio = {$this->getPrecio()}, stock = {$this->getStock()}
      ";
      if ($this->getImagen() != null) {
        $sql .= ", imagen = '{$this->getImagen()}'";
      }
      $sql .= " WHERE id = {$this->getId()};";

      $editar = $this->db->query($sql);
      $resul = false;

      if ($editar) {
        $resul = true;
      }

      return $resul;        
    }
  }
?>