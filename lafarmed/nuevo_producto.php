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

// GUARDAR PRODUCTO
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo = trim($_POST["codigo"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $stock = $_POST["stock"] ?? "";
    $precio = $_POST["precio"] ?? "";
    $vencimiento = $_POST["vencimiento"] ?? "";
    $lote = trim($_POST["lote"] ?? "");

    // VALIDACIÓN
    if ($codigo == "" || $nombre == "" || $categoria == "" || $stock == "" || $precio == "" || $vencimiento == "" || $lote == "") {
        $mensaje = "Error: Todos los campos son obligatorios.";
    } else {
        // Validar que el stock y precio sean números válidos
        if (!is_numeric($stock) || $stock < 0) {
            $mensaje = "Error: El stock debe ser un número válido mayor o igual a 0.";
        } elseif (!is_numeric($precio) || $precio < 0) {
            $mensaje = "Error: El precio debe ser un número válido mayor o igual a 0.";
        } else {
            // Usar prepared statements para seguridad
            $sql = "INSERT INTO productos (codigo, nombre, categoria, stock, precio, vencimiento, lote)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conexion->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssids", $codigo, $nombre, $categoria, $stock, $precio, $vencimiento, $lote);
                
                if ($stmt->execute()) {
                    $mensaje = "✅ Producto registrado correctamente.";
                    // Limpiar el formulario después de guardar exitosamente
                    $_POST = array();
                } else {
                    // Verificar si es error de código duplicado
                    if (strpos($conexion->error, "Duplicate entry") !== false) {
                        $mensaje = "Error: El código del producto ya existe. Por favor, use otro código.";
                    } else {
                        $mensaje = "Error al guardar: " . $conexion->error;
                    }
                }
                $stmt->close();
            } else {
                $mensaje = "Error: No se pudo preparar la consulta.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Nuevo Producto – LAFARMED</title>

    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .form-container {
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        .form-card {
            background: #0a1a2a;
            padding: 35px;
            border-radius: 15px;
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            color: white;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #bcdcff;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            border: 2px solid #1f3b4f;
            background: #1a1a1a;
            color: white;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: rgb(0, 136, 255);
            box-shadow: 0 0 0 3px rgba(0, 136, 255, 0.2);
        }

        button[type="submit"] {
            width: 100%;
            background-color: rgb(0, 136, 255);
            padding: 14px;
            border-radius: 10px;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 136, 255, 0.4);
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: rgb(0, 80, 170);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 136, 255, 0.5);
        }

        .mensaje {
            background: rgba(0, 136, 255, 0.2);
            color: #9bd2ff;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid rgb(0, 136, 255);
        }

        .mensaje.error {
            background: rgba(255, 80, 80, 0.2);
            color: #ff8080;
            border-left-color: rgb(255, 80, 80);
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
            <a href="nuevo_producto.php" class="nav-link active">Nuevo Producto</a>
            <a href="buscar.php" class="nav-link">Buscar</a>
        </nav>
        <div class="header-actions">
            <span class="theme-toggle">🌙</span>
            <span class="user-info"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
            <a href="logout.php" class="logout-btn">Salir</a>
        </div>
    </div>
</div>

<div class="form-container">
    <h2>Registrar Nuevo Producto</h2>

    <div class="form-card">
        <?php if ($mensaje != "") { ?>
            <div class="mensaje <?php echo strpos($mensaje, 'Error') !== false ? 'error' : ''; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php } ?>

        <form method="POST" id="formProducto">
            <div class="form-group">
                <label>Código del Medicamento</label>
                <input type="text" name="codigo" placeholder="Ej: MED001" value="<?php echo isset($_POST['codigo']) ? htmlspecialchars($_POST['codigo']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Nombre del Medicamento</label>
                <input type="text" name="nombre" placeholder="Ej: Paracetamol 500mg" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" required>
                    <option value="">Seleccione categoría</option>
                    <option value="Analgésicos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == 'Analgésicos') ? 'selected' : ''; ?>>Analgésicos</option>
                    <option value="Antibióticos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == 'Antibióticos') ? 'selected' : ''; ?>>Antibióticos</option>
                    <option value="Antialérgicos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == 'Antialérgicos') ? 'selected' : ''; ?>>Antialérgicos</option>
                    <option value="Gastrointestinal" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == 'Gastrointestinal') ? 'selected' : ''; ?>>Gastrointestinal</option>
                    <option value="Cardiovascular" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == 'Cardiovascular') ? 'selected' : ''; ?>>Cardiovascular</option>
                    <option value="Antidiabéticos" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == 'Antidiabéticos') ? 'selected' : ''; ?>>Antidiabéticos</option>
                </select>
            </div>

            <div class="form-group">
                <label>Stock Disponible</label>
                <input type="number" name="stock" placeholder="0" min="0" value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Precio</label>
                <input type="number" step="0.01" name="precio" placeholder="0.00" min="0" value="<?php echo isset($_POST['precio']) ? htmlspecialchars($_POST['precio']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Fecha de Vencimiento</label>
                <input type="date" name="vencimiento" value="<?php echo isset($_POST['vencimiento']) ? htmlspecialchars($_POST['vencimiento']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Código de Lote</label>
                <input type="text" name="lote" placeholder="Ej: LOT001" value="<?php echo isset($_POST['lote']) ? htmlspecialchars($_POST['lote']) : ''; ?>" required>
            </div>

            <button type="submit">Guardar Producto</button>
        </form>
        
        <script>
            // Limpiar formulario después de guardar exitosamente
            <?php if (isset($mensaje) && strpos($mensaje, 'correctamente') !== false) { ?>
                setTimeout(function() {
                    document.getElementById('formProducto').reset();
                }, 2000);
            <?php } ?>
        </script>
    </div>
</div>

</body>
</html>
