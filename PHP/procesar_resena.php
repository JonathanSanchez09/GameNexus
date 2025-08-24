<?php
// Se elimina session_start() porque ya se inicia en header.php

// Incluimos el script de ayuda para otorgar logros
include 'otorgar_logro.php';
// Incluimos la conexión a la base de datos
include 'conexion.php';


$mensaje = "";

if (!isset($_SESSION['usuario_id'])) {
    die("No has iniciado sesión.");
}

// Se elimina la conexión redundante, ya que se incluye en conexion.php
// $conn = new mysqli("db", "usuario", "contrasena", "tienda_videojuegos");
// if ($conn->connect_error) {
//     die("Conexión fallida: " . $conn->connect_error);
// }

// Usa la conexión $conn proporcionada por conexion.php
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
            
            // --- INICIO DE LA LÓGICA DE LOGROS ---

            // Obtener el total de reseñas del usuario
            $stmt_resenas_total = $conn->prepare("SELECT COUNT(*) AS total FROM resenas WHERE usuario_id = ?");
            $stmt_resenas_total->bind_param("i", $usuario_id);
            $stmt_resenas_total->execute();
            $total_resenas = $stmt_resenas_total->get_result()->fetch_assoc()['total'];
            $stmt_resenas_total->close();

            // Logro 1: Voz de la Comunidad (3 reseñas)
            if ($total_resenas >= 3) {
                otorgar_logro($conn, $usuario_id, 'tres_resenas');
            }
            
            // Logro 2: El Crítico Maestro (10 reseñas)
            if ($total_resenas >= 10) {
                otorgar_logro($conn, $usuario_id, 'diez_resenas');
            }
            
            // --- FIN DE LA LÓGICA DE LOGROS ---

            $mensaje = "✅ Reseña agregada correctamente.";
            // Ejecutar script de recomendaciones
            exec("docker-compose run --rm recomendador >> /dev/null 2>&1 &");
        } else {
            $mensaje = "❌ Error al insertar la reseña: " . $stmt->error;
        }

        $stmt->close();
    }
}
