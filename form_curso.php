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

    $nombre = $_POST['nombre'] ?? '';
    $objetivo = $_POST['objetivo'] ?? '';
    $fk_instructor = intval($_POST['fk_instructor'] ?? 0);
    $fk_carrera = intval($_POST['fk_carrera'] ?? 0);

    // ARCHIVO CRONOGRAMA (OPCIONAL)
    $cronograma_path = null;

    if (isset($_FILES['cronograma']) && $_FILES['cronograma']['error'] === 0) {

        $allowed = ['pdf', 'png', 'jpg', 'jpeg'];
        $ext = strtolower(pathinfo($_FILES['cronograma']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $cronograma_path = 'uploads/cronogramas/' . time() . '_' . basename($_FILES['cronograma']['name']);
            move_uploaded_file($_FILES['cronograma']['tmp_name'], $cronograma_path);
        } else {
            $mensaje_error = "El archivo debe ser PDF o imagen (PNG/JPG).";
        }
    }

    // INSERTAR EN BD
    if (!isset($mensaje_error)) {

        $stmt = $conn->prepare("
            INSERT INTO cursos (nom_curso, objetivo, fk_instructor, fk_carrera, cronograma)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssiis", $nombre, $objetivo, $fk_instructor, $fk_carrera, $cronograma_path);

        if ($stmt->execute()) {
            $mensaje_exito = "✔️ El curso fue registrado correctamente.";
        } else {
            $mensaje_error = "Ocurrió un error al guardar el curso.";
        }
    }
}

// Instructores
$instructores = $conn->query("SELECT pk_instructor, nombres, apaterno, amaterno FROM instructores WHERE estatus = 1");

// Carreras
$carreras = $conn->query("SELECT pk_carrera, nom_carrera FROM carreras WHERE estatus = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Curso</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
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

        input[type="text"], textarea, select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
            height: 44px;
        }

        textarea {
            height: 90px !important;
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
        }

        button:hover { background-color: #2c4c6e; }

        .back-link {
            display: block;
            text-align: center;
            margin-bottom: 18px;
            text-decoration: none;
            color: #1b2c3b;
            font-weight: bold;
        }

        /* 🔵 MODAL */
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
            z-index: 9999;
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

        @keyframes fadeIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<br><br><br>
<body>

<!-- MODAL -->
<div class="modal" id="modalMsg">
    <div class="modal-box">
        <h3 id="modalTexto"></h3>
        <button onclick="cerrarModal()">Aceptar</button>
    </div>
</div>

<div class="main-content">
    <br><br>
    <div class="form-container">

        <a href="admin.php" class="back-link">⬅ Volver al Panel</a>

        <h2>Agregar Curso</h2>

        <form method="POST" enctype="multipart/form-data">

            <label>Nombre del Curso:</label>
            <input type="text" name="nombre" required>

            <label>Objetivo:</label>
            <textarea name="objetivo" required></textarea>

            <label>Instructor:</label>
            <select name="fk_instructor" required>
                <option value="">-- Selecciona un Instructor --</option>
                <?php while($i = $instructores->fetch_assoc()): ?>
                    <option value="<?= $i['pk_instructor'] ?>">
                        <?= $i['nombres']." ".$i['apaterno']." ".$i['amaterno'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Carrera:</label>
            <select name="fk_carrera" required>
                <option value="">-- Selecciona una Carrera --</option>
                <?php while($c = $carreras->fetch_assoc()): ?>
                    <option value="<?= $c['pk_carrera'] ?>">
                        <?= $c['nom_carrera'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Actividades (opcional):</label>
            <input type="file" name="cronograma" accept=".pdf,.png,.jpg,.jpeg">

            <button type="submit">Guardar Curso</button>
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

    <?php if (isset($mensaje_exito)) : ?>
        window.location.href = "lista_cursos.php";
    <?php endif; ?>
}
</script>

<?php if (isset($mensaje_exito)) : ?>
<script>mostrarModal("<?= $mensaje_exito ?>");</script>
<?php endif; ?>

<?php if (isset($mensaje_error)) : ?>
<script>mostrarModal("❌ <?= $mensaje_error ?>");</script>
<?php endif; ?>

</body>
</html>
