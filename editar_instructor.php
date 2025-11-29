<?php
session_start();
include 'header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

$error = "";
$mensaje_exito = "";
$mensaje_error = "";

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pk_instructor = intval($_POST['pk_instructor'] ?? 0);
    $nombres = trim($_POST['nombres'] ?? '');
    $apaterno = trim($_POST['apaterno'] ?? '');
    $amaterno = trim($_POST['amaterno'] ?? '');
    $estatus = intval($_POST['estatus'] ?? 1);

    if ($pk_instructor > 0 && $nombres !== '' && $apaterno !== '' && $amaterno !== '') {
        $cv_path = null;

        // Subir nuevo CV
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['pdf','doc','docx'];
            $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                if (!is_dir('uploads/cvs')) mkdir('uploads/cvs',0777,true);
                $cv_path = 'uploads/cvs/' . time() . '_' . basename($_FILES['cv']['name']);
                move_uploaded_file($_FILES['cv']['tmp_name'],$cv_path);
            } else {
                $error = "Formato no válido. Solo PDF, DOC o DOCX.";
            }
        }

        $sql = "UPDATE instructores SET nombres = ?, apaterno = ?, amaterno = ?, estatus = ?";
        $params = [$nombres, $apaterno, $amaterno, $estatus];
        $types = "sssi";

        if ($cv_path !== null) {
            $sql .= ", cv = ?";
            $params[] = $cv_path;
            $types .= "s";
        }

        $sql .= " WHERE pk_instructor = ?";
        $params[] = $pk_instructor;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                $mensaje_exito = "Instructor actualizado correctamente";
            } else {
                $mensaje_error = "Error al actualizar: ".$stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje_error = "Error en la consulta: ".$conn->error;
        }
    } else {
        $mensaje_error = "Por favor completa todos los campos obligatorios.";
    }
}

// GET instructor
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) header("Location: lista_instructores.php");

    $stmt = $conn->prepare("SELECT pk_instructor, nombres, apaterno, amaterno, estatus, cv FROM instructores WHERE pk_instructor = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) header("Location: lista_instructores.php");
    $instructor = $res->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Instructor</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin:0; padding:0; min-height:100vh; display:flex; justify-content:center; align-items:flex-start;
}
.main {
    width:100%; display:flex; justify-content:center; padding:40px 20px;
}
.form-container {
    background:#fff; padding:30px 35px; max-width:500px; width:100%; border-radius:12px;
    box-shadow:0 6px 16px rgba(0,0,0,0.1); margin-top:30px;
}
h2 { text-align:center; color:#1b2c3b; margin-bottom:25px; font-size:22px; }
label { font-weight:bold; display:block; margin-bottom:6px; }
input[type="text"], select, input[type="file"] {
    width:100%; padding:10px; margin-bottom:18px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box;
}
button {
    width:100%; padding:12px; background-color:#1b2c3b; color:#fff; border:none; border-radius:6px;
    font-weight:bold; cursor:pointer; transition: background-color 0.3s; margin-top:8px;
}
button:hover { background-color:#2c4c6e; }
.back-link {
    display:block; text-align:center; margin-top:18px; text-decoration:none; color:#1b2c3b; font-weight:bold;
}
.back-link:hover { color:#2c4c6e; }
.cv-actual { background:#f0f0f0; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px; }
.cv-actual a { color:#1b2c3b; font-weight:bold; text-decoration:none; }
.cv-actual a:hover { text-decoration:underline; }

/* 🔵 MODAL */
.modal {
    position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6);
    display:flex; justify-content:center; align-items:center; visibility:hidden; opacity:0; transition:0.3s; z-index:9999;
}
.modal.active { visibility:visible; opacity:1; }
.modal-box {
    background:white; padding:25px; border-radius:12px; width:90%; max-width:400px; text-align:center;
    box-shadow:0 3px 10px rgba(0,0,0,0.25); animation:fadeIn 0.3s;
}
@keyframes fadeIn { from { transform: scale(0.9); opacity:0;} to { transform: scale(1); opacity:1;} }
.modal-btn {
    margin-top:15px; padding:10px; width:100%; border:none; background:#1b2c3b; color:white; font-weight:bold;
    border-radius:6px; cursor:pointer;
}
.modal-btn:hover { background:#2c4c6e; }
</style>
</head>
<body>

<!-- 🔵 MODAL -->
<div class="modal" id="modalMsg">
    <div class="modal-box">
        <h3 id="modalTexto"></h3>
        <button class="modal-btn" onclick="cerrarModal()">Aceptar</button>
    </div>
</div>

<div class="main">
    <div class="form-container">
        <h2>Editar Instructor</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pk_instructor" value="<?= htmlspecialchars($instructor['pk_instructor']) ?>">

            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" required value="<?= htmlspecialchars($instructor['nombres']) ?>">

            <label for="apaterno">Apellido Paterno:</label>
            <input type="text" id="apaterno" name="apaterno" required value="<?= htmlspecialchars($instructor['apaterno']) ?>">

            <label for="amaterno">Apellido Materno:</label>
            <input type="text" id="amaterno" name="amaterno" required value="<?= htmlspecialchars($instructor['amaterno']) ?>">

            <label for="estatus">Estatus:</label>
            <select id="estatus" name="estatus">
                <option value="1" <?= ($instructor['estatus']==1?'selected':'') ?>>Activo</option>
                <option value="0" <?= ($instructor['estatus']==0?'selected':'') ?>>Inactivo</option>
            </select>

            <?php if (!empty($instructor['cv'])): ?>
            <div class="cv-actual">
                <strong>CV actual:</strong> <a href="<?= htmlspecialchars($instructor['cv']) ?>" target="_blank">Ver archivo</a>
            </div>
            <?php endif; ?>

            <label for="cv">Nuevo Currículum (PDF/DOC/DOCX):</label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">

            <button type="submit">Actualizar Instructor</button>
        </form>

        <a href="lista_instructores.php" class="back-link">⬅ Volver a la lista</a>
    </div>
</div>

<script>
function mostrarModal(texto) {
    document.getElementById("modalTexto").innerText = texto;
    document.getElementById("modalMsg").classList.add("active");
}

function cerrarModal() {
    document.getElementById("modalMsg").classList.remove("active");
    window.location.href = "lista_instructores.php";
}

// Mostrar mensajes
<?php if ($mensaje_exito): ?>
    mostrarModal("<?= htmlspecialchars($mensaje_exito) ?>");
<?php endif; ?>
<?php if ($mensaje_error): ?>
    mostrarModal("❌ <?= htmlspecialchars($mensaje_error) ?>");
<?php endif; ?>
</script>

<?php $conn->close(); ?>
</body>
</html>
