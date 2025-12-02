<?php
session_start();
if (!isset($_SESSION['usuario'])) { 
    header("Location: login.php"); 
    exit; 
}
include "conexion.php";

// Headers para prevenir cache en páginas protegidas
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Buscar Producto – LAFARMED</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .search-container {
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        .search-form {
            background: #0a1a2a;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            gap: 15px;
        }

        .search-form input {
            flex: 1;
            padding: 12px 16px;
            border-radius: 10px;
            border: 2px solid #1f3b4f;
            background: #1a1a1a;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-form input:focus {
            outline: none;
            border-color: rgb(0, 136, 255);
            box-shadow: 0 0 0 3px rgba(0, 136, 255, 0.2);
        }

        .search-form button {
            padding: 12px 30px;
            background-color: rgb(0, 136, 255);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 136, 255, 0.4);
        }

        .search-form button:hover {
            background-color: rgb(0, 80, 170);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 136, 255, 0.5);
        }

        h1 {
            color: white;
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: 600;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="header-top">
    <div class="header-left">
        <div class="logo-icon">+</div>
        <span class="logo-text">LAFARMED</span>
    </div>
    <div class="header-right">
        <nav class="header-nav">
            <a href="index.php" class="nav-link">Productos</a>
            <a href="nuevo_producto.php" class="nav-link">Nuevo Producto</a>
            <a href="buscar.php" class="nav-link active">Buscar</a>
        </nav>
        <div class="header-actions">
            <span class="theme-toggle">🌙</span>
            <span class="user-info"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
            <a href="logout.php" class="logout-btn">Salir</a>
        </div>
    </div>
</div>

<div class="search-container">
    <h1>Buscar Producto</h1>

    <form method="GET" class="search-form">
        <input type="text" name="q" placeholder="🔍 Buscar por nombre o código" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit">Buscar</button>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Vencimiento</th>
                </tr>
            </thead>
            <tbody>

<?php
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = "%" . $_GET['q'] . "%";
    $sql = "SELECT * FROM productos WHERE nombre LIKE ? OR codigo LIKE ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $stockStyle = $row['stock'] < 10 ? 'style="color: #e74c3c; font-weight: 600;"' : '';
            echo "<tr>";
            echo "<td><strong>".htmlspecialchars($row['codigo'])."</strong></td>";
            echo "<td>".htmlspecialchars($row['nombre'])."</td>";
            echo "<td><span style='background: rgba(0, 136, 255, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 12px;'>".htmlspecialchars($row['categoria'])."</span></td>";
            echo "<td $stockStyle>".htmlspecialchars($row['stock'])."</td>";
            echo "<td>".htmlspecialchars($row['vencimiento'])."</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='no-results'>No se encontraron productos</td></tr>";
    }
}
?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
