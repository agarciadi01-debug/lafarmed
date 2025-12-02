DROP DATABASE IF EXISTS lafarmed;
CREATE DATABASE IF NOT EXISTS lafarmed;
USE lafarmed;

-- --------------------------------------------------------
-- TABLA: productos
-- --------------------------------------------------------
DROP TABLE IF EXISTS productos;

CREATE TABLE productos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  categoria VARCHAR(50) NOT NULL,
  stock INT(11) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  vencimiento DATE NOT NULL,
  lote VARCHAR(30) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO productos (codigo, nombre, categoria, stock, precio, vencimiento, lote) VALUES
('PAR500', 'Paracetamol 500mg', 'Analgésicos', 150, 5.50, '2025-12-30', 'A123'),
('IBU400', 'Ibuprofeno 400mg', 'Analgésicos', 8, 6.75, '2024-11-29', 'B456'),
('AMO500', 'Amoxicilina 500mg', 'Antibióticos', 45, 12.00, '2025-12-14', 'C789'),
('LOR10', 'Loratadina 10mg', 'Antialérgicos', 75, 7.20, '2026-05-19', 'D101'),
('OME20', 'Omeprazol 20mg', 'Gastrointestinal', 5, 15.30, '2025-08-14', 'E112'),
('MET850', 'Metformina 850mg', 'Antidiabéticos', 80, 16.90, '2025-12-04', 'H151');

-- --------------------------------------------------------
-- TABLA: usuarios
-- --------------------------------------------------------
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id INT(11) NOT NULL AUTO_INCREMENT,
  usuario VARCHAR(50) NOT NULL,
  password VARCHAR(50) NOT NULL,
  rol VARCHAR(30) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usuarios (usuario, password, rol) VALUES
('admin', '123456', 'Administrador'),
('admin', 'admin123', 'Administrador');
