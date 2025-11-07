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

// Usa la conexión $conn proporcionada por conexion.php
// ✅ SQLi: Consulta simple, pero se asume que $conn->query usa Sentencias Preparadas si es necesario.
$juegos_result = $conn->query("SELECT id, nombre FROM juegos");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    
    // 🛡️ PROTECCIÓN XSS EN LA ENTRADA: Sanitizar el comentario
    // Convertimos cualquier carácter especial de HTML (<, >) a su entidad antes de guardarlo.
    $comentario = trim(htmlspecialchars($_POST['comentario'] ?? '', ENT_QUOTES, 'UTF-8'));
    
    // ✅ SQLi: Casteo a entero para asegurar que sean valores numéricos
    $juego_id = (int)($_POST['juego_id'] ?? 0);
    $calificacion = (int)($_POST['calificacion'] ?? 0);

    if (empty($comentario)) {
        $mensaje = "El comentario no puede estar vacío.";
    } elseif ($juego_id <= 0) {
        $mensaje = "Debes seleccionar un juego válido.";
    } elseif ($calificacion < 1 || $calificacion > 5) {
        $mensaje = "La calificación debe estar entre 1 y 5.";
    } else {
        // ✅ PROTECCIÓN SQLi: Se usan Sentencias Preparadas (Prepared Statements)
        $stmt = $conn->prepare("INSERT INTO resenas (juego_id, usuario_id, comentario, calificacion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $juego_id, $usuario_id, $comentario, $calificacion);

        if ($stmt->execute()) {
            // NOTA: Con los TRIGGERS implementados en la base de datos, 
            // este bloque de UPDATE es redundante, pero lo mantengo por si no usas TRIGGERS.
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
            
            // --- INICIO DE LA LÓGICA DE LOGROS (sin cambios) ---

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
            // NOTA: Asegúrate de que el comando docker-compose use la sintaxis moderna (sin guion)
            exec("docker compose run --rm recomendador >> /dev/null 2>&1 &");
        } else {
            $mensaje = "❌ Error al insertar la reseña: " . $stmt->error;
        }

        $stmt->close();
    }
}