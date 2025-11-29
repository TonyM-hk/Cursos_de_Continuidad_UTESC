<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario_header = $_SESSION['usuario'] ?? null;
$tipo_header = ($usuario_header) ? 'Administrador' : null;

include 'header2.php';
?>

<header>
    <div class="header-left">
        <a href="index.php">
            <img src="img/LOGO_UTESC.png" alt="Logo UTESC">
        </a>

        <!-- BOTÓN INICIO PEGADO AL LOGO -->
        <a href="index.php" class="btn-home">Inicio</a>
    </div>

    <div class="header-center">
        <h1>CURSOS DE CONTINUIDAD</h1>

        <?php if ($usuario_header): ?>
            <div class="admin-fixed-label">
                <span class="admin-user"><?php echo $usuario_header; ?></span>
                <span class="admin-role">(<?php echo $tipo_header; ?>)</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="header-right">
        <?php if ($usuario_header): ?>
            <a href="admin.php" class="btn-admin" title="Panel de Administración">⚙️</a>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</header>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding-top: 80px;
}

header {
    background-color: #1b2c3b;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 999;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* ----------- LEFT AREA ----------- */

.header-left {
    display: flex;
    align-items: center;
    gap: 15px; /* separa el logo del botón Inicio */
}

.header-left img {
    height: 50px;
}

/* ----------- CENTER AREA ----------- */

.header-center {
    flex: 1;
    text-align: center;
}

.header-center h1 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
}

/* ESTILO USUARIO ADMINISTRADOR */
.admin-fixed-label {
    margin-top: 5px;
    background: #0f2230;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-block;
    box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    font-size: 13px;
    color: #ecf0f1;
}

.admin-user {
    font-weight: bold;
    color: #58d68d;
}

.admin-role {
    color: #bdc3c7;
    margin-left: 5px;
}

/* ----------- RIGHT AREA ----------- */

.header-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ----------- BUTTONS ----------- */

/* Inicio pegado al logo */
.btn-home {
    background-color: #2980b9;
    color: #fff;
    padding: 7px 14px;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
}
.btn-home:hover { background-color: #1f6390; }

.btn-login {
    color: #1b2c3b;
    background-color: #fff;
    padding: 7px 14px;
    border-radius: 6px;
    font-weight: bold;
    margin-right: 20px;
}
.btn-login:hover { background-color: #e0e0e0; }

.btn-logout {
    background-color: #e74c3c;
    color: #fff;
    padding: 7px 14px;
    border-radius: 6px;
    font-weight: bold;
    margin-right: 20px;
}
.btn-logout:hover { background-color: #c0392b; }

.btn-admin {
    font-size: 24px;
    color: #fff;
}
.btn-admin:hover {
    transform: rotate(15deg);
    color: #f1c40f;
}

/* ----------- RESPONSIVE ----------- */

@media (max-width: 600px) {
    header {
        flex-direction: column;
        text-align: center;
        padding: 10px;
    }

    .header-left {
        justify-content: center;
    }

    .header-center {
        margin-top: 5px;
    }

    .header-right {
        margin-top: 5px;
        justify-content: center;
    }
}
.header-right a,
.header-left a {
    text-decoration: none !important;
}

</style>
