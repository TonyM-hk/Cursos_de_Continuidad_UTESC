<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['accion'])) {
    die("Parámetros incompletos.");
}

$id = intval($_GET['id']);
$accion = $_GET['accion'];

if ($accion === 'habilitar') {
    $nuevo_estatus = 1;
    $mensaje = "✅ Instructor habilitado correctamente.";
} elseif ($accion === 'deshabilitar') {
    $nuevo_estatus = 0;
    $mensaje = "🚫 Instructor deshabilitado correctamente.";
} else {
    die("Acción no válida.");
}

// Actualizar 
$sql = "UPDATE instructores SET estatus = ? WHERE pk_instructor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $nuevo_estatus, $id);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = $mensaje;
} else {
    $_SESSION['mensaje'] = "❌ Error al actualizar el estatus.";
}

$stmt->close();
$conn->close();

header("Location: lista_instructores.php");
exit();
?>
