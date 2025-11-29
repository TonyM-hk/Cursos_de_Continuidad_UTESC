<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

$id = $_POST['id'] ?? 0;
$nombre = $_POST['nombre'] ?? '';

if ($id && $nombre != "") {
    $stmt = $conn->prepare("UPDATE carreras SET nom_carrera = ? WHERE pk_carrera = ?");
    $stmt->bind_param("si", $nombre, $id);

    if ($stmt->execute()) {
        // ✅ Redirigir incluyendo mensaje de éxito
        $mensaje = urlencode("Carrera actualizada correctamente");
        header("Location: editar_carrera.php?id=$id&exito=$mensaje");
        exit();
    } else {
        $mensaje = urlencode("Error al actualizar: " . $conn->error);
        header("Location: editar_carrera.php?id=$id&error=$mensaje");
        exit();
    }
} else {
    $mensaje = urlencode("Datos inválidos.");
    header("Location: editar_carrera.php?id=$id&error=$mensaje");
    exit();
}
?>
