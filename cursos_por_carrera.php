<?php
include 'conexion.php';
include 'header.php';

if (!isset($_GET['carrera'])) {
    echo "<h2>Debe seleccionar una carrera.</h2>";
    exit;
}

$carrera = $_GET['carrera'];

// Seleccionar cursos con objetivo
$sql = "SELECT nom_curso, objetivo 
        FROM cursos 
        WHERE fk_carrera = (
            SELECT pk_carrera FROM carreras WHERE nom_carrera = ?
        )
        AND estatus = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $carrera);
$stmt->execute();
$result = $stmt->get_result();

$cursos = [];
while ($row = $result->fetch_assoc()) {
    // Icono según curso
    switch($row['nom_curso']){
        case "Contabilidad Básica": $icono = "fas fa-calculator"; break;
        case "Programación": $icono = "fas fa-laptop-code"; break;
        case "Administración Financiera": $icono = "fas fa-briefcase"; break;
        case "Turismo y Hospitalidad": $icono = "fas fa-bullhorn"; break;
        case "Derecho Civil": $icono = "fas fa-gavel"; break;
        case "Economía Aplicada": $icono = "fas fa-chart-line"; break;
        default: $icono = "fas fa-book";
    }

    $cursos[] = [
        "nombre" => $row['nom_curso'],
        "icono" => $icono,
        "objetivo" => $row['objetivo']
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cursos de <?= htmlspecialchars($carrera) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { 
    font-family: Arial, sans-serif; 
    background:#f4f4f9; 
    margin:0; 
    color:#333; 
}
main { 
    padding:50px 20px; 
    text-align:center; 
}
h2 { 
    color:#1b2c3b; 
    margin-bottom:40px; 
}

/* Contenedor de cursos */
.cursos-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); 
    gap: 35px; /* Separación mayor entre cuadros */
    justify-items: center;
}
@media (max-width: 768px) { .cursos-container { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .cursos-container { grid-template-columns: 1fr; } }

/* Estilo de cada curso - glassmorphism */
.curso {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    width: 100%;
    max-width: 240px;
    padding: 25px 20px;
    text-align: center;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #1b2c3b;
    position: relative;
}

/* Tooltip con objetivo */
.curso .tooltip {
    visibility: hidden;
    width: 220px;
    background-color: #1b2c3b;
    color: #fff;
    text-align: center;
    padding: 12px;
    border-radius: 12px;
    position: absolute;
    top: -20px; /* Separa un poco más el tooltip de la tarjeta */
    left: 50%;
    transform: translate(-50%, -100%);
    opacity: 0;
    transition: opacity 0.3s, transform 0.3s;
    font-size: 14px;
    z-index: 100;
    pointer-events: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Flecha del tooltip */
.curso .tooltip::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    border-width: 6px;
    border-style: solid;
    border-color: #1b2c3b transparent transparent transparent;
}

.curso:hover .tooltip {
    visibility: visible;
    opacity: 1;
    transform: translate(-50%, -110%); /* Se eleva un poco más al hacer hover */
}

.curso:hover {
    transform: translateY(-10px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.2);
    background: rgba(255, 255, 255, 0.95);
}

.curso i {
    font-size: 60px;
    margin-bottom: 14px;
    color: #1b2c3b;
    transition: transform 0.3s, color 0.3s;
}

.curso:hover i {
    transform: scale(1.15);
    color: #2c4c6e;
}

.curso p {
    font-weight: 600;
    font-size: 16px;
    margin: 0;
}

/* Botón de volver */
.back {
    display:inline-block;
    margin-top:30px;
    text-decoration:none;
    color:#1b2c3b;
    font-weight:bold;
    transition: color 0.3s;
}
.back:hover { color:#2c4c6e; text-decoration:underline; }
</style>
</head>
<body>

<main>
    <a href="index.php" class="back">⬅ Atrás</a>
    <h2>Cursos de <?= htmlspecialchars($carrera) ?></h2>
    <br><br>
    <?php if(count($cursos) > 0): ?>
    <div class="cursos-container">
        <?php foreach($cursos as $curso): ?>
            <a href="detalle_curso.php?curso=<?= urlencode($curso['nombre']) ?>" class="curso">
                <i class="<?= $curso['icono'] ?>"></i>
                <p><?= htmlspecialchars($curso['nombre']) ?></p>
                <div class="tooltip"><?= htmlspecialchars($curso['objetivo']) ?></div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p>No hay cursos disponibles para esta carrera.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
