<?php
include "conexion.php";

header("Content-Type: application/json; charset=utf-8");

// OBTENER PRODUCTOS
$sql = "SELECT 
            codigo,
            nombre,
            categoria,
            stock,
            precio,
            vencimiento AS fecha_vencimiento,
            lote,
            DATEDIFF(vencimiento, NOW()) AS dias_vencimiento,
            
            -- Si está próximo a vencer (menos de 30 días)
            CASE 
                WHEN DATEDIFF(vencimiento, NOW()) <= 30 THEN 1
                ELSE 0
            END AS proximo_vencer

        FROM productos";

$res = $conexion->query($sql);

$productos = [];

while ($row = $res->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);
