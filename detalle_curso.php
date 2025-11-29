<?php
include 'conexion.php';
include 'header.php';



if (!isset($_GET['curso'])) {
    echo "<h2>Debe seleccionar un curso.</h2>";
    exit;
}

$curso = $_GET['curso'];

// detalles del curso
$sql = "SELECT c.nom_curso, c.objetivo, c.fk_instructor, c.cronograma, i.nombres, i.apaterno, i.amaterno, i.cv, c.fk_carrera 
        FROM cursos c
        INNER JOIN instructores i ON c.fk_instructor = i.pk_instructor
        WHERE c.nom_curso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $curso);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>Curso no encontrado.</h2>";
    exit;
}

$curso_detalle = $result->fetch_assoc();

// nombre de la carrera
$sql2 = "SELECT nom_carrera FROM carreras WHERE pk_carrera = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $curso_detalle['fk_carrera']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$carrera = $result2->fetch_assoc()['nom_carrera'];

$stmt->close();
$stmt2->close();
$conn->close();

// Rutas de archivos
$cvPath = $curso_detalle['cv'] ? 'uploads/cvs/' . $curso_detalle['cv'] : '';
$cronogramaPath = $curso_detalle['cronograma'] ? 'uploads/' . $curso_detalle['cronograma'] : '';
$cronogramaFile = $curso_detalle['cronograma'] ? __DIR__ . '/uploads/' . $curso_detalle['cronograma'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detalle de <?= htmlspecialchars($curso_detalle['nom_curso']) ?></title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f9;
    margin:0;
    color:#243642;
}

main {
    padding:30px 20px;
    max-width:1000px;
    margin:auto;
}

/* Botón estilo página de cursos */
a.back {
    display:inline-block;
    margin-top:30px;
    text-decoration:none;
    color:#1b2c3b;
    font-weight:bold;
}
a.back:hover { text-decoration:underline; }

h2 {
    text-align:center;
    font-size:28px;
    font-weight:700;
    color:#1b2c3b;
    margin-bottom:20px;
}


.detalle-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    grid-template-rows: auto auto auto; 
    gap: 20px;
}

.carrera-box { grid-column: 1; grid-row: 1; }
.instructor-box { grid-column: 1; grid-row: 2; }


.objetivo-box { 
    grid-column: 2; 
    grid-row: 1 / span 2;
}


.cronograma-box { 
    grid-column: 1 / span 2; 
    grid-row: 3;
    text-align: center; 
}


.detalle-box {
    background:#ffffff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 8px rgba(0,0,0,0.05);
    font-size:16px;
    line-height:1.5;
    transition: transform 0.2s, box-shadow 0.2s;
}
.detalle-box:hover {
    transform: translateY(-3px);
    box-shadow:0 8px 15px rgba(0,0,0,0.1);
}


.cronograma-box strong {
    display: block;
    margin-bottom: 15px;
    font-size: 18px;
    color: #1b2c3b;
}

.cronograma-box img, .cronograma-box iframe {
    width: 95%; 
    height: 500px; 
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}


.cv-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #1b2c3b;
    font-weight:600;
    transition: color 0.2s ease;
}

.cv-link span {
    opacity:0;
    transform: translateX(-10px);
    color: #3b5998;
    font-weight:600;
    font-size:0.95em;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.cv-link:hover {
    color:#3b5998;
}
.cv-link:hover span {
    opacity:1;
    transform: translateX(0);
}

@media (max-width:800px) {
    .detalle-grid {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
    .carrera-box, .objetivo-box, .instructor-box, .cronograma-box { grid-column:1; grid-row:auto; }
}
</style>
</head>
<body>

<main>

      <br>
        <br>
        <br>
    <a href="cursos_por_carrera.php?carrera=<?= urlencode($carrera) ?>" class="back">⬅ Atrás</a>

    <h2><?= htmlspecialchars($curso_detalle['nom_curso']) ?></h2>

    <div class="detalle-grid">
        <!-- Carrera -->
        <div class="detalle-box carrera-box">
            <strong>Carrera:</strong><br>
            <?= htmlspecialchars($carrera) ?>
        </div>

        <!-- Instructor -->
        <div class="detalle-box instructor-box">
            <strong>Instructor:</strong><br>
            <a href="ver_cv.php?id_instructor=<?= $curso_detalle['fk_instructor'] ?>" class="cv-link">
                <?= htmlspecialchars($curso_detalle['nombres'] . ' ' . $curso_detalle['apaterno'] . ' ' . $curso_detalle['amaterno']) ?>
                <span>➤ Ver CV</span>
            </a>
        </div>

        <!-- Objetivo -->
        <div class="detalle-box objetivo-box">
            <strong>Objetivo:</strong><br>
            <?= htmlspecialchars($curso_detalle['objetivo']) ?>
        </div>

        <!-- Cronograma -->
        <?php if(!empty($cronogramaPath) && file_exists($cronogramaFile)): ?>
        <div class="detalle-box cronograma-box">
            <strong>Actividades:</strong>
            <?php
            $ext = strtolower(pathinfo($curso_detalle['cronograma'], PATHINFO_EXTENSION));
            if($ext === 'pdf'): ?>
                <iframe src="<?= htmlspecialchars($cronogramaPath) ?>"></iframe>
            <?php else: ?>
                <img src="<?= htmlspecialchars($cronogramaPath) ?>" alt="Cronograma de <?= htmlspecialchars($curso_detalle['nom_curso']) ?>">
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
