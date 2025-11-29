<?php
session_start();
include 'header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

$error = "";
$mensaje = "";

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pk_curso      = intval($_POST['pk_curso'] ?? 0);
    $nom_curso     = trim($_POST['nom_curso'] ?? '');
    $objetivo      = trim($_POST['objetivo'] ?? '');
    $fk_instructor = intval($_POST['fk_instructor'] ?? 0);
    $fk_carrera    = intval($_POST['fk_carrera'] ?? 0);

    if ($pk_curso > 0 && $nom_curso !== '' && $objetivo !== '' && $fk_instructor > 0 && $fk_carrera > 0) {
        $cronograma_nombre = null;
        if (isset($_FILES['cronograma']) && $_FILES['cronograma']['error'] === UPLOAD_ERR_OK) {
            $archivo_tmp = $_FILES['cronograma']['tmp_name'];
            $archivo_nombre = time() . '_' . basename($_FILES['cronograma']['name']);
            $ruta_destino = "uploads/" . $archivo_nombre;

            if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
                $cronograma_nombre = $archivo_nombre;
            } else {
                $error = "Error al subir el archivo del cronograma.";
            }
        }

        $sql = "UPDATE cursos SET nom_curso = ?, objetivo = ?, fk_instructor = ?, fk_carrera = ?";
        $params = [$nom_curso, $objetivo, $fk_instructor, $fk_carrera];
        $types = "ssii";

        if ($cronograma_nombre !== null) {
            $sql .= ", cronograma = ?";
            $params[] = $cronograma_nombre;
            $types .= "s";
        }

        $sql .= " WHERE pk_curso = ?";
        $params[] = $pk_curso;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                $mensaje = urlencode("Curso actualizado correctamente");
                header("Location: editar_curso.php?id=$pk_curso&msg=$mensaje");
                exit();
            } else {
                $error = "Error al actualizar: " . $stmt->error;
            }
        } else {
            $error = "Error en la consulta: " . $conn->error;
        }
    } else {
        $error = "Por favor completa todos los campos.";
    }
}

// GET: traer curso
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        header("Location: lista_cursos.php");
        exit();
    }

    $sql = "SELECT pk_curso, nom_curso, objetivo, fk_instructor, fk_carrera, cronograma FROM cursos WHERE pk_curso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        header("Location: lista_cursos.php");
        exit();
    }
    $curso = $res->fetch_assoc();
    $stmt->close();
}

// Traer instructores
$instructores = [];
$r = $conn->query("SELECT pk_instructor, nombres, apaterno, amaterno FROM instructores WHERE estatus = 1 ORDER BY nombres");
while ($row = $r->fetch_assoc()) $instructores[] = $row;

// Traer carreras
$carreras = [];
$r = $conn->query("SELECT pk_carrera, nom_carrera FROM carreras WHERE estatus = 1 ORDER BY nom_carrera");
while ($row = $r->fetch_assoc()) $carreras[] = $row;

// Mensajes GET
$mensaje_exito = $_GET['msg'] ?? null;
$mensaje_error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Editar Curso</title>
<style>
/* Mantener estilos originales, body, card, modales, etc. */
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; margin:0; padding:0; }
.main-content { display:flex; justify-content:center; align-items:flex-start; min-height:100vh; padding:120px 20px 60px; }
.card { background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.2); width:100%; max-width:500px; }
h2 { text-align:center; color:#1b2c3b; margin-bottom:25px; }
label { display:block; font-weight:bold; margin-bottom:8px; color:#333; }
input[type="text"], textarea, select, input[type="file"] { width:100%; padding:11px; margin-bottom:18px; border:1px solid #ccc; border-radius:6px; font-size:15px; box-sizing:border-box; }
textarea { height:90px !important; }
button { width:100%; padding:12px; background-color:#1b2c3b; color:#fff; border:none; border-radius:6px; font-weight:bold; font-size:16px; cursor:pointer; transition:0.3s; }
button:hover { background-color:#2c4c6e; }
.back { display:block; text-align:center; margin-top:15px; text-decoration:none; color:#1b2c3b; font-weight:bold; transition:0.3s; }
.back:hover { color:#2c4c6e; }
.error { background:#ffe6e6; padding:10px; border-radius:6px; color:#a00; margin-bottom:15px; text-align:center; }
/* MODAL */
.modal { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); display:flex; justify-content:center; align-items:center; visibility:hidden; opacity:0; transition:0.3s; z-index:9999; }
.modal.active { visibility:visible; opacity:1; }
.modal-box { background:white; padding:25px; border-radius:12px; width:90%; max-width:400px; text-align:center; box-shadow:0 3px 10px rgba(0,0,0,0.25); animation:fadeIn 0.3s; }
@keyframes fadeIn { from { transform: scale(0.9); opacity:0; } to { transform: scale(1); opacity:1; } }
.modal-btn { margin-top:15px; padding:10px; width:100%; border:none; background:#1b2c3b; color:white; font-weight:bold; border-radius:6px; cursor:pointer; }
.modal-btn:hover { background:#2c4c6e; }
</style>
</head>
<body>

<!-- MODAL -->
<div class="modal" id="modalMsg">
    <div class="modal-box">
        <h3 id="modalTexto"></h3>
        <button class="modal-btn" onclick="cerrarModal()">Aceptar</button>
    </div>
</div>

<div class="main-content">
    <div class="card">
        <h2>Editar Curso</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="editar_curso.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pk_curso" value="<?= intval($curso['pk_curso'] ?? 0) ?>">

            <label>Nombre del Curso:</label>
            <input type="text" name="nom_curso" required value="<?= htmlspecialchars($curso['nom_curso'] ?? '') ?>">

            <label>Objetivo:</label>
            <textarea name="objetivo" required><?= htmlspecialchars($curso['objetivo'] ?? '') ?></textarea>

            <label>Instructor:</label>
            <select name="fk_instructor" required>
                <option value="">-- Selecciona un Instructor --</option>
                <?php foreach ($instructores as $i): ?>
                    <option value="<?= $i['pk_instructor'] ?>" <?= ($curso['fk_instructor'] ?? 0) == $i['pk_instructor'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($i['nombres'].' '.$i['apaterno'].' '.$i['amaterno']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Carrera:</label>
            <select name="fk_carrera" required>
                <option value="">-- Selecciona una Carrera --</option>
                <?php foreach ($carreras as $c): ?>
                    <option value="<?= $c['pk_carrera'] ?>" <?= ($curso['fk_carrera'] ?? 0) == $c['pk_carrera'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nom_carrera']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Actividades (PDF o Imagen):</label>
            <input type="file" name="cronograma" accept=".pdf,image/*">

            <button type="submit">Actualizar Curso</button>
        </form>

        <a href="lista_cursos.php" class="back">⬅ Volver a la lista</a>
    </div>
</div>

<script>
function mostrarModal(texto) {
    document.getElementById("modalTexto").innerText = texto;
    document.getElementById("modalMsg").classList.add("active");
}

function cerrarModal() {
    document.getElementById("modalMsg").classList.remove("active");
    window.location.href = "lista_cursos.php";
}

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
