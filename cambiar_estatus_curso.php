<?php
require "conexion.php";

if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];

    if ($accion === "deshabilitar") {
        $sql = "UPDATE cursos SET estatus = 0 WHERE pk_curso = $id";
    } elseif ($accion === "habilitar") {
        $sql = "UPDATE cursos SET estatus = 1 WHERE pk_curso = $id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: lista_cursos.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
