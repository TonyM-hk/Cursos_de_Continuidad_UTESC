<?php
session_start();
include 'header.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require "conexion.php";

// PAGINACIÓN
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina > 1) ? ($pagina - 1) * $por_pagina : 0;

// Contar total de carreras
$sql_count = "SELECT COUNT(*) as total FROM carreras";
$result_count = $conn->query($sql_count);
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta principal con LIMIT
$sql = "SELECT pk_carrera, nom_carrera, estatus 
        FROM carreras 
        ORDER BY nom_carrera 
        LIMIT $inicio, $por_pagina";
$result = $conn->query($sql);

// Mensajes
$mensaje_exito = $_GET["exito"] ?? null;
$mensaje_error = $_GET["error"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Listado de Carreras</title>

<style>
/* Tus estilos originales */
.main-content { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
h2 { text-align: center; color: #1b2c3b; margin-bottom: 10px; }
.buttons-container { text-align: center; margin-bottom: 20px; }
.btn-back, .btn-add { display: inline-block; margin: 5px 10px; text-decoration: none; background: #2c3e50; color: #fff; padding: 10px 20px; border-radius: 6px; transition: background 0.3s; font-weight: bold; cursor: pointer; }
.btn-back:hover, .btn-add:hover { background: #1a242f; }
table { width: 80%; margin: 0 auto 20px auto; border-collapse: collapse; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; }
th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
th { background-color: #1b2c3b; color: white; }
tr:hover { background-color: #f1f1f1; }
a.action-btn, button.action-btn { text-decoration: none; color: white; padding: 6px 12px; border-radius: 6px; transition: background 0.3s; font-size: 14px; margin: 0 3px; border: none; display: inline-block; cursor: pointer; }
.btn-edit { background: #3498db; }     
.btn-disable { background: #e74c3c; }  
.btn-enable { background: #27ae60; }   
.btn-edit:hover, .btn-disable:hover, .btn-enable:hover { opacity: 0.9; }
/* MODAL GENERAL */
.modal { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); display:flex; justify-content:center; align-items:center; visibility:hidden; opacity:0; transition:0.3s; z-index:9999; }
.modal.active { visibility: visible; opacity: 1; }
.modal-box { background:white; padding:25px; border-radius:12px; width:90%; max-width:380px; text-align:center; animation: fadeIn 0.3s; box-shadow: 0 3px 10px rgba(0,0,0,0.25); }
@keyframes fadeIn { from { transform: scale(0.9); opacity:0; } to { transform: scale(1); opacity:1; } }
.modal-btn { margin-top:15px; padding:10px; width:100%; border:none; background:#1b2c3b; color:white; font-weight:bold; border-radius:6px; cursor:pointer; }
.modal-btn:hover { background:#2c4c6e; }
/* PAGINACIÓN */
.pagination a { text-decoration: none; color: #1b2c3b; padding: 6px 10px; border: 1px solid #ddd; border-radius: 6px; margin: 0 3px; transition:0.3s; }
.pagination a:hover { background: #ddd; }
.pagination a.active { font-weight: bold; background: #1b2c3b; color:white; }
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
function abrirConfirm(accionUrl, texto) { urlAccion = accionUrl; document.getElementById("confirmTexto").innerText = texto; document.getElementById("modalConfirm").classList.add("active"); }
function confirmarAccion() { window.location.href = urlAccion; }
function cerrarModalConfirm() { document.getElementById("modalConfirm").classList.remove("active"); }
function mostrarModalMensaje(texto) { document.getElementById("modalTexto").innerText = texto; document.getElementById("modalMsg").classList.add("active"); }
function cerrarModalMensaje() { document.getElementById("modalMsg").classList.remove("active"); window.location.href = "lista_carreras.php"; }
</script>

<h2>Listado de Carreras</h2>

<div class="buttons-container">
    <a href="form_carrera.php" class="btn-add">➕ Agregar Nueva Carrera</a>
    <a href="admin.php" class="btn-back">⬅ Volver al Panel</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre de la Carrera</th>
        <th>Estatus</th>
        <th>Acciones</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['pk_carrera']; ?></td>
        <td><?= $row['nom_carrera']; ?></td>
        <td><?= $row['estatus'] == 1 ? "✅ Activa" : "🚫 Inactiva"; ?></td>
        <td>
            <a href="editar_carrera.php?id=<?= $row['pk_carrera']; ?>" class="action-btn btn-edit">✏ Editar</a>
            <?php if ($row['estatus'] == 1): ?>
                <button class="action-btn btn-disable"
                    onclick="abrirConfirm(
                        'cambiar_estatus_carrera.php?id=<?= $row['pk_carrera']; ?>&accion=deshabilitar',
                        '¿Seguro que deseas DESHABILITAR esta carrera?'
                    )">
                    Deshabilitar
                </button>
            <?php else: ?>
                <button class="action-btn btn-enable"
                    onclick="abrirConfirm(
                        'cambiar_estatus_carrera.php?id=<?= $row['pk_carrera']; ?>&accion=habilitar',
                        '¿Seguro que deseas HABILITAR esta carrera?'
                    )">
                    Habilitar
                </button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- PAGINACIÓN -->
<div class="pagination" style="text-align:center; margin-bottom:30px;">
    <?php if($pagina > 1): ?>
        <a href="?pagina=<?= $pagina-1 ?>">« Anterior</a>
    <?php endif; ?>

    <?php for($i=1; $i<=$total_paginas; $i++): ?>
        <a href="?pagina=<?= $i ?>" class="<?= $pagina==$i?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if($pagina < $total_paginas): ?>
        <a href="?pagina=<?= $pagina+1 ?>">Siguiente »</a>
    <?php endif; ?>
</div>

</div> <!-- Fin main-content -->

<?php $conn->close(); ?>

<?php if ($mensaje_exito): ?>
    <script>mostrarModalMensaje("<?= $mensaje_exito ?>");</script>
<?php endif; ?>
<?php if ($mensaje_error): ?>
    <script>mostrarModalMensaje("❌ <?= $mensaje_error ?>");</script>
<?php endif; ?>

</body>
</html>
