<?php
require "conexion.php";

if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];

    if ($accion === "deshabilitar") {
        $sql = "UPDATE carreras SET estatus = 0 WHERE pk_carrera = $id";
    } elseif ($accion === "habilitar") {
        $sql = "UPDATE carreras SET estatus = 1 WHERE pk_carrera = $id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: lista_carreras.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
