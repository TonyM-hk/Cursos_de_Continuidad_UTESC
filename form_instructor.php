<?php
session_start();
include 'header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

// FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = $_POST['nombres'] ?? '';
    $apaterno = $_POST['apaterno'] ?? '';
    $amaterno = $_POST['amaterno'] ?? '';
    $estatus = intval($_POST['estatus'] ?? 1);

    // ARCHIVO CV (OPCIONAL)
    $cv_path = null;

    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {

        $allowed = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $cv_path = 'uploads/cvs/' . time() . '_' . basename($_FILES['cv']['name']);
            move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path);
        } else {
            $mensaje_error = "Formato de archivo no válido. Solo se permiten PDF, DOC y DOCX.";
        }
    }

    // INSERTAR EN BD
    if (!isset($mensaje_error)) {

        $stmt = $conn->prepare("INSERT INTO instructores (nombres, apaterno, amaterno, estatus, cv) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $nombres, $apaterno, $amaterno, $estatus, $cv_path);

        if ($stmt->execute()) {
            $mensaje_exito = "✔️ El instructor fue registrado correctamente.";
        } else {
            $mensaje_error = "Ocurrió un error al guardar el instructor.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Instructor</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .main-content {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 120px 20px 60px;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            max-width: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #1b2c3b;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            height: 44px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #1b2c3b;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        button:hover {
            background-color: #2c4c6e;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-bottom: 18px;
            text-decoration: none;
            color: #1b2c3b;
            font-weight: bold;
        }

        /* 🔵 MODAL PROFESIONAL */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            visibility: hidden;
            opacity: 0;
            transition: 0.3s;
        }

        .modal.active {
            visibility: visible;
            opacity: 1;
        }

        .modal-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.25);
            animation: fadeIn 0.3s;
        }

        .modal-box h3 {
            margin-bottom: 15px;
            color: #1b2c3b;
        }

        .modal-box button {
            background: #1b2c3b;
            margin-top: 10px;
        }

        @keyframes fadeIn {
            from { transform: scale(0.90); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<!-- MODAL DE MENSAJE -->
<div class="modal" id="modalMsg">
    <div class="modal-box">
        <h3 id="modalTexto"></h3>
        <button onclick="cerrarModal()">Aceptar</button>
    </div>
</div>

<div class="main-content">
    <div class="form-container">

        <a href="admin.php" class="back-link">⬅ Volver al Panel</a>

        <h2>Agregar Instructor</h2>

        <form method="POST" enctype="multipart/form-data">
            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" required>

            <label for="apaterno">Apellido Paterno:</label>
            <input type="text" id="apaterno" name="apaterno" required>

            <label for="amaterno">Apellido Materno:</label>
            <input type="text" id="amaterno" name="amaterno" required>

            <label for="estatus">Estatus:</label>
            <select id="estatus" name="estatus">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>

            <label for="cv">Currículum (PDF/DOC/DOCX) — Opcional:</label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">

            <button type="submit">Guardar Instructor</button>
        </form>

    </div>
</div>

<script>
function mostrarModal(texto) {
    document.getElementById("modalTexto").innerText = texto;
    document.getElementById("modalMsg").classList.add("active");
}

function cerrarModal() {
    document.getElementById("modalMsg").classList.remove("active");

    // Si era éxito, redirige
    <?php if (isset($mensaje_exito)) : ?>
        window.location.href = "lista_instructores.php";
    <?php endif; ?>
}
</script>

<?php if (isset($mensaje_exito)) : ?>
<script>
    mostrarModal("<?= $mensaje_exito ?>");
</script>
<?php endif; ?>

<?php if (isset($mensaje_error)) : ?>
<script>
    mostrarModal("❌ <?= $mensaje_error ?>");
</script>
<?php endif; ?>

</body>
</html>
