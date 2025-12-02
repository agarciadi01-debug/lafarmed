<?php
session_start();
require "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND rol = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $usuario, $rol);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $fila = $resultado->fetch_assoc();

        if ($fila['password'] === $password) {

            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = $fila['rol'];

            header("Location: index.php");
            exit();
        }
    }
}

header("Location: login.php?error=1");
exit();
