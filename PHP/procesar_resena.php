<?php
$mensaje = "";

if (!isset($_SESSION['usuario_id'])) {
    die("No has iniciado sesión.");
}

$conn = new mysqli("localhost", "root", "", "tienda_videojuegos");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$juegos_result = $conn->query("SELECT id, nombre FROM juegos");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $comentario = trim($_POST['comentario'] ?? '');
    $juego_id = (int)($_POST['juego_id'] ?? 0);
    $calificacion = (int)($_POST['calificacion'] ?? 0);

    if (empty($comentario)) {
        $mensaje = "El comentario no puede estar vacío.";
    } elseif ($juego_id <= 0) {
        $mensaje = "Debes seleccionar un juego válido.";
    } elseif ($calificacion < 1 || $calificacion > 5) {
        $mensaje = "La calificación debe estar entre 1 y 5.";
    } else {
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
