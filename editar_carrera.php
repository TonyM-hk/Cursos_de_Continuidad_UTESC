<?php
session_start();
include 'header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM carreras WHERE pk_carrera = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$carrera = $result->fetch_assoc();

if (!$carrera) {
    echo "Carrera no encontrada.";
    exit();
}

// MENSAJES RECIBIDOS DESDE update_carrera.php
$mensaje_exito = $_GET["exito"] ?? null;
$mensaje_error = $_GET["error"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Carrera</title>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.form-container {
    background-color: #fff;
    padding: 30px;
    width: 100%;
    max-width: 400px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.form-container h2 {
    margin-bottom: 20px;
    color: #1b2c3b;
    text-align: center;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 8px;
}

input[type="text"], button {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    box-sizing: border-box;
}

input[type="text"] {
    border: 1px solid #ccc;
    margin-bottom: 15px;
}

button {
    background-color: #1b2c3b;
    border: none;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #2c4c6e;
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 15px;
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

.modal-box button {
    width: 100%;
    margin-top: 15px;
    padding: 10px;
    border: none;
    background: #1b2c3b;
    color: white;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
}

.modal-box button:hover { background:#2c4c6e; }
</style>
</head>

<body>

<!-- 🔵 MODAL -->
<div class="modal" id="modalMsg">
    <div class="modal-box">
        <h3 id="modalTexto"></h3>
        <button onclick="cerrarModal()">Aceptar</button>
    </div>
</div>

<div class="form-container">
    <a href="lista_carreras.php" class="back-link">⬅ Volver a Carreras</a>

    <h2>Editar Carrera</h2>

    <form action="update_carrera.php" method="POST">
        <input type="hidden" name="id" value="<?= $carrera['pk_carrera']; ?>">

        <label for="nombre">Nombre de la Carrera:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($carrera['nom_carrera']); ?>" required>

        <button type="submit">Actualizar Carrera</button>
    </form>
</div>

<script>
function mostrarModal(texto) {
    document.getElementById("modalTexto").innerText = texto;
    document.getElementById("modalMsg").classList.add("active");
}

function cerrarModal() {
    document.getElementById("modalMsg").classList.remove("active");
    <?php if ($mensaje_exito) : ?>
        window.location.href = "lista_carreras.php";
    <?php endif; ?>
}
</script>

<?php if ($mensaje_exito) : ?>
<script>mostrarModal("<?= $mensaje_exito ?>");</script>
<?php endif; ?>

<?php if ($mensaje_error) : ?>
<script>mostrarModal("❌ <?= $mensaje_error ?>");</script>
<?php endif; ?>

</body>
</html>
