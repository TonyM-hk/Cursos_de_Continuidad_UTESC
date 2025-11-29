<?php
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_curso    = $_POST['nom_curso'];
    $objetivo     = $_POST['objetivo'];
    $fk_instructor = $_POST['fk_instructor'];
    $fk_carrera    = $_POST['fk_carrera'];

    //  archivo cronograma
    $cronograma_nombre = null;
    if (isset($_FILES['cronograma']) && $_FILES['cronograma']['error'] === UPLOAD_ERR_OK) {
        $archivo_tmp = $_FILES['cronograma']['tmp_name'];
        $archivo_nombre = basename($_FILES['cronograma']['name']);
        $ruta_destino = "uploads/" . $archivo_nombre;

        if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
            $cronograma_nombre = $archivo_nombre;
        } else {
            echo "Error al subir el archivo del cronograma.";
            exit();
        }
    }

    $sql = "INSERT INTO cursos (nom_curso, objetivo, fk_instructor, fk_carrera, estatus, cronograma)
            VALUES (?, ?, ?, ?, 1, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiis", $nom_curso, $objetivo, $fk_instructor, $fk_carrera, $cronograma_nombre);

    if ($stmt->execute()) {
        header("Location: admin.php?msg=Curso agregado correctamente");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
