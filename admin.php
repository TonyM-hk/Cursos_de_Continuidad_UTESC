<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require 'header.php';
?>

<?php if (isset($_SESSION['login_exitoso'])): ?>
    <div id="mensaje-bienvenida" class="mensaje-bienvenida">
        Bienvenido administrador
    </div>

    <!-- Script para ocultarlo a los 5 segundos -->
    <script>
        setTimeout(() => {
            const msg = document.getElementById("mensaje-bienvenida");
            if (msg) {
                msg.style.transition = "opacity 0.3s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500);
            }
        }, 5000);
    </script>

    <?php unset($_SESSION['login_exitoso']); ?>
<?php endif; ?>


<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<br><br><br>
<title>Panel de Administración - UTESC</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f3f8;
        margin: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        color: #1b2c3b;
    }

    main {
        flex: 1;
        padding: 60px 20px;
        text-align: center;
    }

    h2 {
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 50px;
        color: #1b2c3b;
    }

    .menu-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 10px;
    }

    .menu-item {
        padding: 35px 20px;
        border-radius: 18px;
        box-shadow: 6px 6px 16px rgba(0,0,0,0.08), -6px -6px 16px rgba(255,255,255,0.7);
        transition: all 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .menu-item a {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #fff;
        font-weight: 600;
        font-size: 17px;
        gap: 12px;
        transition: transform 0.3s, color 0.3s;
    }

    .menu-item a i {
        font-size: 28px;
        transition: transform 0.3s;
    }

    .menu-item:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 8px 8px 20px rgba(0,0,0,0.12), -8px -8px 20px rgba(255,255,255,0.7);
    }

    .menu-item:hover a i {
        transform: scale(1.2);
    }

    /* Colores sobrios */
    .menu-item.form { background: #2f4856ff; }   /* Azul grisáceo */
    .menu-item.list { background: #3e783dff; }   /* Verde grisáceo */

    footer {
        background-color: #1b2c3b;
        color: #fff;
        text-align: center;
        padding: 15px 0;
        font-size: 14px;
    }

    @media (max-width:1024px){
        .menu-container { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width:600px){
        .menu-container { grid-template-columns: 1fr; }
        h2 { font-size: 24px; }
        .menu-item a { font-size: 16px; }
        .menu-item a i { font-size: 24px; }
    }
    .mensaje-bienvenida {
    position: fixed;
    top: 85px;
    right: 20px;
    background: #27ae60;
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 2000;
    animation: aparecer 0.4s ease-out;
}

@keyframes aparecer {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>
</head>

<body>

<?php include 'header.php';?>


<main>
    <h2>Panel de Administración - UTESC</h2>
    <div class="menu-container">
        <!-- Formularios -->
        <div class="menu-item form">
            <a href="form_carrera.php"><i class="fas fa-clipboard-list"></i>Agregar Carrera</a>
        </div>
        <div class="menu-item form">
            <a href="form_curso.php"><i class="fas fa-plus-circle"></i>Agregar Curso</a>
        </div>
        <div class="menu-item form">
            <a href="form_instructor.php"><i class="fas fa-chalkboard-teacher"></i>Agregar Instructor</a>
        </div>

        <!-- Listas -->
        <div class="menu-item list">
            <a href="lista_carreras.php"><i class="fas fa-file-alt"></i>Lista de Carreras</a>
        </div>
        <div class="menu-item list">
            <a href="lista_cursos.php"><i class="fas fa-book"></i>Lista de Cursos</a>
        </div>
        <div class="menu-item list">
            <a href="lista_instructores.php"><i class="fas fa-users"></i>Lista de Instructores</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
