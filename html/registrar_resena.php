<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tienda_videojuegos");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_SESSION['usuario_id'])) {
    die("No has iniciado sesión.");
}

// Obtener los juegos disponibles para el select
$juegos_result = $conn->query("SELECT id, nombre FROM juegos");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $comentario = trim($_POST['comentario']);
    $juego_id = (int)$_POST['juego_id'];
    $calificacion = (int)$_POST['calificacion'];

    if (!empty($comentario) && $juego_id > 0 && $calificacion >= 1 && $calificacion <= 5) {
        // Insertar la reseña
        $stmt = $conn->prepare("INSERT INTO resenas (juego_id, usuario_id, comentario, calificacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $juego_id, $usuario_id, $comentario, $calificacion);

        if ($stmt->execute()) {
            // Actualizar calificación promedio y cantidad de reseñas en la tabla juegos
            $update = "
                UPDATE juegos 
                SET calificacion_promedio = (SELECT AVG(calificacion) FROM resenas WHERE juego_id = ?),
                    cantidad_resenas = (SELECT COUNT(*) FROM resenas WHERE juego_id = ?)
                WHERE id = ?
            ";
            $upstmt = $conn->prepare($update);
            $upstmt->bind_param("iii", $juego_id, $juego_id, $juego_id);
            $upstmt->execute();
            $upstmt->close();

            echo "Reseña agregada correctamente.";
        } else {
            echo "Error al insertar la reseña: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Todos los campos son obligatorios y la calificación debe ser entre 1 y 5.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Reseña</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'encabezado_nav.php'; ?>

    <div class="container">
        <h2>Agregar Reseña</h2>
        <form class="review-form" method="POST" action="">
            <select name="juego_id" required>
                <option value="">Selecciona un juego</option>
                <?php if ($juegos_result && $juegos_result->num_rows > 0): ?>
                    <?php while ($juego = $juegos_result->fetch_assoc()): ?>
                        <option value="<?= $juego['id'] ?>"><?= htmlspecialchars($juego['nombre']) ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <textarea name="comentario" placeholder="Tu reseña" required></textarea>
            
            <!-- Campo para calificación -->
            <div class="rating">
                <label>Calificación (1-5):</label>
                <div>
                    <input type="radio" id="rating1" name="calificacion" value="1" required>
                    <label for="rating1">1</label>
                    <input type="radio" id="rating2" name="calificacion" value="2" required>
                    <label for="rating2">2</label>
                    <input type="radio" id="rating3" name="calificacion" value="3" required>
                    <label for="rating3">3</label>
                    <input type="radio" id="rating4" name="calificacion" value="4" required>
                    <label for="rating4">4</label>
                    <input type="radio" id="rating5" name="calificacion" value="5" required>
                    <label for="rating5">5</label>
                </div>
            </div>

            <button type="submit">Enviar Reseña</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Tienda de Videojuegos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
