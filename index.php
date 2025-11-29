<?php
session_start();
include 'conexion.php';
require 'header2.php';


$sql = "SELECT nom_carrera FROM carreras WHERE estatus=1";
$result = $conn->query($sql);

$carreras = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        switch ($row['nom_carrera']) {
            case "Contaduría":
                $icono = "fas fa-calculator";
                $color = "#ff62c8ff";
                break;
            case "Agricultura":
                $icono = "fas fa-seedling";
                $color = "#27ae60";
                break;
            case "Enfermería":
                $icono = "fas fa-user-nurse";
                $color = "#1abc9c";
                break;
            case "Gastronomía":
                $icono = "fas fa-utensils";
                $color = "#e67e22";
                break;
            case "Mantenimiento":
                $icono = "fas fa-tools";
                $color = "#7f8c8d";
                break;
            case "Mecatrónica":
                $icono = "fas fa-robot";
                $color = "#8e44ad";
                break;
            case "Alimentos":
                $icono = "fa-solid fa-flask-vial";
                $color = "#c0392b";
                break;
            case "Tecnologías":
                $icono = "fas fa-laptop-code";
                $color = "#2980b9";
                break;
            case "Turismo":
                $icono = "fas fa-plane-departure";
                $color = "#f39c12";
                break;
            default:
                $icono = "fas fa-school";
                $color = "#34495e";
        }

        $carreras[] = [
            "nombre" => $row['nom_carrera'],
            "icono" => $icono,
            "color" => $color
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos de Continuidad - UTESC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }

        main h2 {
            text-align: center;
            color: #16222A;
            font-size: 28px;
            margin: 80px 20px 50px;
            font-weight: 700;
        }

        .cursos-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 35px;
            padding: 0 40px 80px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .curso {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px 25px 35px;
            text-align: left;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: #243642;
            display: flex;
            align-items: center;
            gap: 20px;
            border-left: 8px solid transparent;
        }

        .curso:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        }

        .curso i {
            font-size: 55px;
            border-radius: 16px;
            padding: 20px;
            background: #f0f4f8;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .curso:hover i {
            transform: scale(1.1);
            filter: brightness(1.1);
        }

        .curso p {
            font-weight: 600;
            font-size: 19px;
            margin: 0;
            transition: color 0.3s ease;
        }

        .curso small {
            display: block;
            color: #6c7a89;
            font-size: 14px;
            margin-top: 4px;
        }

        /* Colores dinámicos */
        <?php foreach ($carreras as $curso): ?>
        .curso[data-carrera="<?= htmlspecialchars($curso['nombre']); ?>"] {
            border-left-color: <?= $curso['color']; ?>;
        }

        .curso[data-carrera="<?= htmlspecialchars($curso['nombre']); ?>"] i {
            color: <?= $curso['color']; ?>;
        }

        .curso[data-carrera="<?= htmlspecialchars($curso['nombre']); ?>"]:hover {
            border-left-color: <?= $curso['color']; ?>;
            box-shadow: 0 10px 25px <?= $curso['color']; ?>33;
        }

        .curso[data-carrera="<?= htmlspecialchars($curso['nombre']); ?>"]:hover p {
            color: <?= $curso['color']; ?>;
        }
        <?php endforeach; ?>
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <br><br><br>
        <h2>La Universidad Tecnológica de Escuinapa ofrece cursos de las siguientes carreras:</h2>
        <div class="cursos-container">
            <?php foreach ($carreras as $curso): ?>
                <a href="cursos_por_carrera.php?carrera=<?= urlencode($curso['nombre']); ?>"
                   class="curso"
                   data-carrera="<?= htmlspecialchars($curso['nombre']); ?>">
                    <i class="<?= $curso['icono']; ?>"></i>
                    <div>
                        <p><?= $curso['nombre']; ?></p>
                        <small>Ver cursos disponibles</small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
