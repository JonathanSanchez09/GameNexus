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

$mensaje = ""; // Para mostrar mensajes en la interfaz

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $comentario = trim($_POST['comentario'] ?? '');
    $juego_id = (int)($_POST['juego_id'] ?? 0);
    $calificacion = (int)($_POST['calificacion'] ?? 0);

    // Validaciones
    if (empty($comentario)) {
        $mensaje = "El comentario no puede estar vacío.";
    } elseif ($juego_id <= 0) {
        $mensaje = "Debes seleccionar un juego válido.";
    } elseif ($calificacion < 1 || $calificacion > 5) {
        $mensaje = "La calificación debe estar entre 1 y 5.";
    } else {
        // Insertar la reseña
        $stmt = $conn->prepare("INSERT INTO resenas (juego_id, usuario_id, comentario, calificacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $juego_id, $usuario_id, $comentario, $calificacion);

        if ($stmt->execute()) {
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

            $mensaje = "✅ Reseña agregada correctamente.";
        } else {
            $mensaje = "❌ Error al insertar la reseña: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>
<?php
include 'header.php';
include 'encabezado_nav.php';
include '../PHP/conexion.php';?>

    <div class="container">
        <h2>Agregar Reseña</h2>
        <?php if (!empty($mensaje)): ?>
            <div class="alerta-mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
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
            <label>Calificación:</label>
            <div>
                <input type="radio" id="rating5" name="calificacion" value="5" required>
                <label for="rating5"></label>

                <input type="radio" id="rating4" name="calificacion" value="4">
                <label for="rating4"></label>

                <input type="radio" id="rating3" name="calificacion" value="3">
                <label for="rating3"></label>

                <input type="radio" id="rating2" name="calificacion" value="2">
                <label for="rating2"></label>

                <input type="radio" id="rating1" name="calificacion" value="1">
                <label for="rating1"></label>
            </div>
        </div>


            <button type="submit">Enviar Reseña</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>