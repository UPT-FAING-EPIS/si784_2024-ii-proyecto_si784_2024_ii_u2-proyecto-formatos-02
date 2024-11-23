CREATE DATABASE tienda;
USE tienda;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20),
    imagen VARCHAR(255),
    PRIMARY KEY (id),
    UNIQUE (email)
) ENGINE=InnoDB;

INSERT INTO usuarios VALUES (NULL, 'Admin', 'Admin', 'admin@admin.com', 'contraseña', 'admin', NULL);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO categorias VALUES (NULL, 'Manga corta');
INSERT INTO categorias VALUES (NULL, 'Tirantes');
INSERT INTO categorias VALUES (NULL, 'Manga larga');
INSERT INTO categorias VALUES (NULL, 'Sudaderas');

CREATE TABLE productos (
    id INT AUTO_INCREMENT NOT NULL,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    oferta VARCHAR(2),
    fecha DATE NOT NULL,
    imagen VARCHAR(255),
    PRIMARY KEY (id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT NOT NULL,
    usuario_id INT NOT NULL,
    provincia VARCHAR(100) NOT NULL,
    localidad VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    coste DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) NOT NULL,
    fecha DATE,
    hora TIME,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE lineas_pedidos (
    id INT AUTO_INCREMENT NOT NULL,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    unidades INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;
