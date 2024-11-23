<?php
  class Categoria
  {
    private $id;
    private $nombre;
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

    public function getNombre()
    {
      return $this->nombre;
    }
    public function setNombre($nombre)
    {
      $this->nombre = $this->db->real_escape_string($nombre);

      return $this;
    }

    public function obtenerTodo()
    {
      $sql = "
        SELECT * FROM categorias
        ORDER BY id DESC;
      ";
      $categorias = $this->db->query($sql);

      return $categorias;
    }

    public function obtenerUno()
    {
      $sql = "
        SELECT * FROM categorias
        WHERE id = {$this->id};
      ";
      $categoria = $this->db->query($sql);

      return $categoria->fetch_object();
    }

    public function guardarBase()
    {
      $sql = "
        INSERT INTO categorias
        VALUES (null, '{$this->getNombre()}');
      ";
      $guardar = $this->db->query($sql);
      $resul = false;

      if ($guardar) {
        $resul = true;
      }

      return $resul;
    }
  }
?>