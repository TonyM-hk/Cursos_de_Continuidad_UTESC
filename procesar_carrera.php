<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php"; 

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === "") {
    header("Location: form_carrera.php?error=El nombre de la carrera no puede estar vacío ❌");
    exit();
}

// INSERTAR EN BD
$stmt = $conn->prepare("INSERT INTO carreras (nom_carrera, estatus) VALUES (?, 1)");
$stmt->bind_param("s", $nombre);

if ($stmt->execute()) {

    header("Location: form_carrera.php?exito=✔️ La carrera fue registrada correctamente");
    exit();

} else {

    header("Location: agregar_carrera.php?error=No se pudo guardar la carrera ❌");
    exit();
}

$stmt->close();
$conn->close();
?>
