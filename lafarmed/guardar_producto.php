<?php
include "conexion.php";

$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$categoria = $_POST['categoria'];
$stock = $_POST['stock'];
$venc = $_POST['vencimiento'];

$sql = "INSERT INTO productos(codigo, nombre, categoria, stock, vencimiento) 
VALUES ('$codigo', '$nombre', '$categoria', '$stock', '$venc')";

if ($conexion->query($sql)) {
    header("Location: index.php");
} else {
    echo "Error: " . $conexion->error;
}
?>
