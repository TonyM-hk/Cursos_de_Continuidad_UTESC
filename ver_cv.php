<?php
require "conexion.php";
include 'header2.php';


if (!isset($_GET['id_instructor'])) {
    echo "<h2>Debe seleccionar un instructor.</h2>";
    exit;
}

$id_instructor = intval($_GET['id_instructor']);

$sql = "SELECT nombres, apaterno, amaterno, cv FROM instructores WHERE pk_instructor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_instructor);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>Instructor no encontrado.</h2>";
    exit;
}

$instructor = $result->fetch_assoc();
$stmt->close();
$conn->close();

$cvFile = __DIR__ . '/' . $instructor['cv']; 
$cvWebPath = $instructor['cv'];             

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CV de <?= htmlspecialchars($instructor['nombres'] . ' ' . $instructor['apaterno'] . ' ' . $instructor['amaterno']) ?></title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f9; margin:0; color:#333; }
main { padding:40px 20px; max-width:900px; margin:auto; }
h2 { color:#1b2c3b; margin-bottom:20px; text-align:center; }
.cv-container { background:#fff; padding:15px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
iframe { width:100%; height:600px; border:none; border-radius:6px; }
a.back { display:inline-block; margin-top:20px; text-decoration:none; color:#1b2c3b; font-weight:bold; }
a.back:hover { text-decoration:underline; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
     <br>
        <br>
        <br>
     <a href="javascript:history.back()" class="back">⬅ Atrás</a>
    <h2><?= htmlspecialchars($instructor['nombres'] . ' ' . $instructor['apaterno'] . ' ' . $instructor['amaterno']) ?></h2>
    
    <div class="cv-container">
    <?php if (!empty($instructor['cv']) && file_exists($cvFile)): ?>

        <iframe src="<?= htmlspecialchars($cvWebPath) ?>" type="application/pdf"></iframe>
    <?php else: ?>
        <p>No se ha subido un CV para este instructor o el archivo no se encuentra.</p>
    <?php endif; ?>
    </div>

   
</main>

<?php include 'footer.php'; ?>
</body>
</html>
