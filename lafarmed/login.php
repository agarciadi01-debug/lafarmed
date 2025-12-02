<?php 
session_start();

// Si ya hay una sesión activa, redirigir al index
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Headers para prevenir cache
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
    <title>Login – LAFARMED</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0a1a2a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-box {
            background-color: #0f2b44;
            padding: 50px 45px;
            border-radius: 15px;
            width: 450px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            color: white;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgb(0, 136, 255);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 30px;
            color: white;
            font-weight: bold;
        }

        .login-box h1 {
            color: white;
            margin-bottom: 8px;
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .login-box .subtitle {
            color: #bcdcff;
            font-size: 14px;
            margin-bottom: 35px;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: #bcdcff;
            font-size: 13px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #1f3b4f;
            background-color: #0a1a2a;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: rgb(0, 136, 255);
            box-shadow: 0 0 0 2px rgba(0, 136, 255, 0.2);
        }

        input::placeholder {
            color: #666;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-group label {
            color: #bcdcff;
            font-size: 13px;
            margin: 0;
            cursor: pointer;
        }

        .btn {
            width: 100%;
            background-color: rgb(0, 136, 255);
            border: none;
            padding: 14px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(0, 100, 200);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 136, 255, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .error {
            color: rgb(255, 80, 80);
            margin-bottom: 20px;
            font-size: 13px;
            background: rgba(255, 80, 80, 0.1);
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="login-box">
    <div class="logo-container">
        <div class="logo-icon">+</div>
        <h1>LAFARMED</h1>
        <p class="subtitle">Gestión de Inventario Farmacéutico</p>
    </div>

    <?php if (isset($_GET['error'])) { ?>
        <div class="error">Usuario, contraseña o rol incorrectos</div>
    <?php } ?>

    <form action="validar_login.php" method="POST">
        <div class="form-group">
            <label for="usuario">Correo Electrónico</label>
            <input type="text" id="usuario" name="usuario" placeholder="admin@lafarmed.com" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="password123" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="">Seleccione el rol</option>
                <option value="Administrador">Administrador</option>
                <option value="Vendedor">Vendedor</option>
            </select>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Recordar sesión</label>
        </div>

        <button class="btn" type="submit">Iniciar Sesión</button>
    </form>
</div>

<script>
// Prevenir que el botón atrás acceda a páginas en cache
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Limpiar el historial del navegador al cargar la página de login
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
</body>
</html>
