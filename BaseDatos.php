<?php
$conexion = mysqli_connect("localhost", "root", "", "");

// Crear base de datos
mysqli_query($conexion, "CREATE DATABASE IF NOT EXISTS libreria");

// Seleccionarla
mysqli_select_db($conexion, "libreria");

// Crear tabla
mysqli_query($conexion, "CREATE TABLE IF NOT EXISTS libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    subcategoria VARCHAR(50),
    anio INT,
    precio DECIMAL(10,2),
    stock INT DEFAULT 0,
    sinopsis TEXT,
    portada VARCHAR(255)
)");

echo "Base de datos y tabla creadas correctamente";
?>