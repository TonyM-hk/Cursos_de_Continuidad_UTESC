<?php
session_start(); 

// Usuario permitido
$usuario_valido = "DirectivoC";

$clave_hash = '$2y$10$88S2sgQK1TYSJObauy0vF.zxWu7MLUFYnPwXWbadXtL77fXFbb9LS';  

$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? '';

// Validación segura
if ($usuario === $usuario_valido && password_verify($clave, $clave_hash)) {

    // Guardar usuario en sesión
    $_SESSION['usuario'] = $usuario;

    // Guardar tipo de usuario
    $_SESSION['tipo'] = 'admin';

    // 🔥 ACTIVAR MENSAJE DE BIENVENIDA
    $_SESSION['login_exitoso'] = true;

    header("Location: admin.php");
    exit(); 

} else {
    header("Location: login.php?error=1");
    exit();
}
?>
