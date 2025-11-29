<?php
include 'header2.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - UTESC</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1b2c3b, #3f648b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 40px 35px;
            width: 100%;
            max-width: 380px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        /* LOGO */
        .logo {
            width: 120px;
            margin-bottom: 15px;
        }

        .login-container h2 {
            margin-bottom: 10px;
            color: #1b2c3b;
            font-size: 26px;
        }

        .login-container p {
            font-size: 14px;
            color: #555;
            margin-bottom: 25px;
        }

        .login-container input {
            width: 100%;
            padding: 13px;
            margin-bottom: 15px;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .login-container input:focus {
            border-color: #1b2c3b;
            outline: none;
            box-shadow: 0 0 5px rgba(27, 44, 59, 0.3);
        }

        .login-container button {
            background-color: #1b2c3b;
            color: white;
            border: none;
            padding: 13px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .login-container button:hover {
            background-color: #2e4963;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1b2c3b;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #3f648b;
            text-decoration: underline;
        }

        /* Mensaje de error */
        .error-msg {
            background: #ffdddd;
            color: #b30000;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: bold;
            border: 1px solid #d99;
        }
    </style>
</head>
<body>
    <div class="login-container">

        <!-- LOGO UTESC -->
        <img src="img/LOGO_UTESC.png" alt="Logo UTESC" class="logo">

        <h2>Iniciar Sesión</h2>
        <p>El inicio de sesión solo está disponible para los directivos</p>

        <!-- Mensaje de error -->
        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="error-msg">
                ❌ Usuario o contraseña incorrectos
            </div>
        <?php endif; ?>

        <form action="procesar_login.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
            <a href="index.php" class="back-link">⬅ Volver al Inicio</a>
        </form>
    </div>
</body>
</html>
