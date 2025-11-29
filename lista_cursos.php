<?php
session_start();
include 'header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

// FILTRO POR CARRERA
$fk_carrera = $_GET['carrera'] ?? '';

// PAGINACIÓN
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina > 1) ? ($pagina - 1) * $por_pagina : 0;

// Construir consulta con filtro
$sql_where = "";
if (!empty($fk_carrera)) {
    $sql_where = "WHERE c.fk_carrera = " . intval($fk_carrera);
}

// Contar total de cursos para paginación
$sql_count = "SELECT COUNT(*) as total FROM cursos c $sql_where";
$result_count = $conn->query($sql_count);
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta principal con LIMIT
$sql = "SELECT c.pk_curso, c.nom_curso, c.estatus, c.cronograma,
               i.nombres, i.apaterno, i.amaterno,
               ca.nom_carrera
        FROM cursos c
        INNER JOIN instructores i ON c.fk_instructor = i.pk_instructor
        INNER JOIN carreras ca ON c.fk_carrera = ca.pk_carrera
        $sql_where
        ORDER BY ca.nom_carrera, c.nom_curso
        LIMIT $inicio, $por_pagina";

$result = $conn->query($sql);

// Obtener todas las carreras para el filtro
$sql_carreras = "SELECT pk_carrera, nom_carrera FROM carreras ORDER BY nom_carrera";
$result_carreras = $conn->query($sql_carreras);

// MENSAJES
$mensaje_exito = $_GET["exito"] ?? null;
$mensaje_error = $_GET["error"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Listado de Cursos</title>

<style>
/* --- TUS ESTILOS ORIGINALES --- */

.main-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #1b2c3b;
    margin-bottom: 10px;
}

.buttons-container {
    text-align: center;
    margin-bottom: 20px;
}

.btn-add,
.btn-back {
    display: inline-block;
    margin: 5px 10px;
    text-decoration: none;
    background: #2c3e50;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    transition: background 0.3s;
    font-weight: bold;
    cursor: pointer;
}

.btn-add:hover,
.btn-back:hover {
    background: #1a242f;
}

table {
    width: 95%;
    margin: 0 auto 20px auto;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #1b2c3b;
    color: white;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Estilos de acción */
a.action-btn, button.action-btn {
    text-decoration: none;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background 0.3s;
    font-size: 14px;
    margin: 3px 0;
    border: none;
    display: inline-block;
    cursor: pointer;
}

.btn-edit { background: #3498db; }
.btn-disable { background: #e74c3c; }
.btn-enable { background: #27ae60; }

.action-btn:hover { opacity: 0.9; }

/* MODALES */
.modal {
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.6);
    display:flex;
    justify-content:center;
    align-items:center;
    visibility:hidden;
    opacity:0;
    transition:0.3s;
    z-index:9999;
}

.modal.active {
    visibility: visible;
    opacity: 1;
}

.modal-box {
    background:white;
    padding:25px;
    border-radius:12px;
    width:90%;
    max-width:380px;
    text-align:center;
    animation: fadeIn 0.3s;
    box-shadow: 0 3px 10px rgba(0,0,0,0.25);
}

@keyframes fadeIn {
    from { transform: scale(0.9); opacity:0; }
    to { transform: scale(1); opacity:1; }
}

.modal-btn {
    margin-top:15px;
    padding:10px;
    width:100%;
    border:none;
    background:#1b2c3b;
    color:white;
    font-weight:bold;
    border-radius:6px;
    cursor:pointer;
}

.modal-btn:hover { background:#2c4c6e; }

/* RESPONSIVE ORIGINAL */
@media (max-width: 768px) {

    table thead { display: none; }

    table, table tbody, table tr, table td {
        display: block;
        width: 100%;
    }

    table tr {
        margin-bottom: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        padding: 10px;
    }

    table td {
        text-align: left;
        padding-left: 50%;
        position: relative;
        border-bottom: 1px solid #eee;
    }

    table td:last-child { border-bottom: none; }

    table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        font-weight: bold;
        color: #1b2c3b;
    }
}

/* 🔥 FIX OFICIAL PARA TUS BOTONES EN CELULAR 🔥 */
@media (max-width: 768px) {

    td[data-label="Acciones"] {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        padding-left: 0 !important;
    }

    td[data-label="Acciones"] .action-btn,
    td[data-label="Acciones"] button.action-btn {
        width: 100% !important;
        text-align: center !important;
        padding: 14px !important;
        font-size: 16px !important;
        border-radius: 10px !important;
    }
}
</style>
</head>

<body>

<div class="main-content">

<!-- MODALES -->
<div class="modal" id="modalMsg">
    <div class="modal-box">
        <h3 id="modalTexto"></h3>
        <button class="modal-btn" onclick="cerrarModalMensaje()">Aceptar</button>
    </div>
</div>

<div class="modal" id="modalConfirm">
    <div class="modal-box">
        <h3 id="confirmTexto">¿Confirmas la acción?</h3>
        <button class="modal-btn" onclick="confirmarAccion()">Sí, continuar</button>
        <button class="modal-btn" style="background:#7f8c8d;" onclick="cerrarModalConfirm()">Cancelar</button>
    </div>
</div>

<script>
let urlAccion = "";
function abrirConfirm(accionUrl, texto) {
    urlAccion = accionUrl;
    document.getElementById("confirmTexto").innerText = texto;
    document.getElementById("modalConfirm").classList.add("active");
}
function confirmarAccion() { window.location.href = urlAccion; }
function cerrarModalConfirm() { document.getElementById("modalConfirm").classList.remove("active"); }
function mostrarModalMensaje(texto) {
    document.getElementById("modalTexto").innerText = texto;
    document.getElementById("modalMsg").classList.add("active");
}
function cerrarModalMensaje() {
    document.getElementById("modalMsg").classList.remove("active");
    window.location.href = "lista_cursos.php";
}
</script>

<h2>Listado de Cursos por Carrera</h2>

<div class="buttons-container">
    <a href="form_curso.php" class="btn-add">➕ Agregar Nuevo Curso</a>
    <a href="admin.php" class="btn-back">⬅ Volver al Panel</a>
</div>

<!-- FILTRO -->
<form method="GET" style="text-align:center; margin-bottom:20px;">
    <label>Filtrar por Carrera:</label>
    <select name="carrera" onchange="this.form.submit()">
        <option value="">Todas</option>
        <?php while ($row_c = $result_carreras->fetch_assoc()): ?>
            <option value="<?= $row_c['pk_carrera'] ?>" <?= $fk_carrera == $row_c['pk_carrera'] ? 'selected' : '' ?>>
                <?= $row_c['nom_carrera'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Curso</th>
        <th>Instructor</th>
        <th>Carrera</th>
        <th>Actividades</th>
        <th>Estatus</th>
        <th>Acciones</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>

        <td data-label="ID"><?= $row['pk_curso']; ?></td>

        <td data-label="Curso"><?= $row['nom_curso']; ?></td>

        <td data-label="Instructor">
            <?= $row['nombres'] . ' ' . $row['apaterno'] . ' ' . $row['amaterno']; ?>
        </td>

        <td data-label="Carrera"><?= $row['nom_carrera']; ?></td>

        <td data-label="Actividades">
            <?php if(!empty($row['cronograma'])): ?>
                <a href="uploads/<?= $row['cronograma']; ?>" target="_blank">📄 Ver</a>
            <?php else: ?>-
            <?php endif; ?>
        </td>

        <td data-label="Estatus">
            <?= $row['estatus'] == 1 ? "✅ Activo" : "🚫 Inactivo"; ?>
        </td>

        <td data-label="Acciones">

            <a href="editar_curso.php?id=<?= $row['pk_curso']; ?>"
               class="action-btn btn-edit">✏ Editar</a>

            <?php if ($row['estatus'] == 1): ?>

                <button class="action-btn btn-disable"
                    onclick="abrirConfirm('cambiar_estatus_curso.php?id=<?= $row['pk_curso']; ?>&accion=deshabilitar',
                    '¿Seguro que deseas DESHABILITAR este curso?')">
                    Deshabilitar
                </button>

            <?php else: ?>

                <button class="action-btn btn-enable"
                    onclick="abrirConfirm('cambiar_estatus_curso.php?id=<?= $row['pk_curso']; ?>&accion=habilitar',
                    '¿Seguro que deseas HABILITAR este curso?')">
                    Habilitar
                </button>

            <?php endif; ?>

        </td>

    </tr>
    <?php endwhile; ?>
</table>

<!-- PAGINACIÓN -->
<div class="pagination" style="text-align:center; margin-top:20px;">
    <?php for($i=1; $i<=$total_paginas; $i++): ?>
        <a href="?pagina=<?= $i ?><?= $fk_carrera ? '&carrera='.$fk_carrera : '' ?>"
           class="<?= $pagina==$i?'active':'' ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>
</div>

</div>

<?php $conn->close(); ?>

<?php if ($mensaje_exito): ?>
    <script>mostrarModalMensaje("<?= $mensaje_exito ?>");</script>
<?php endif; ?>
<?php if ($mensaje_error): ?>
    <script>mostrarModalMensaje("❌ <?= $mensaje_error ?>");</script>
<?php endif; ?>

</body>
</html>
