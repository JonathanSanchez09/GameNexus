<?php
$mensaje = "";
$tipo_mensaje = ""; // 'exito' o 'error'

// Conexión a la base de datos
function conectarDB() {
    $conn = new mysqli("localhost", "root", "", "tienda_videojuegos");
    if ($conn->connect_error) {
        die("Hubo un error al conectar a la base de datos.");
    }
    return $conn;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);
    $precio = (float) $_POST['precio'];
    $imagen = trim($_POST['imagen']);

    if (!empty($nombre) && !empty($descripcion) && !empty($categoria) && $precio >= 0 && !empty($imagen)) {
        $conn = conectarDB();

        // Evitar inyección de SQL
        $stmt_check = $conn->prepare("SELECT id FROM juegos WHERE nombre = ?");
        $stmt_check->bind_param("s", $nombre);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $mensaje = "Ya existe un juego con ese nombre.";
            $tipo_mensaje = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO juegos (nombre, descripcion, categoria, precio, imagen_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $nombre, $descripcion, $categoria, $precio, $imagen);

            if ($stmt->execute()) {
                $mensaje = "Juego agregado correctamente.";
                $tipo_mensaje = "exito";
                // Redirigir al index.php
                header("Location: index.php?mensaje=Juego agregado correctamente");
                exit();
            } else {
                $mensaje = "Error al agregar el juego: " . htmlspecialchars($stmt->error);
                $tipo_mensaje = "error";
            }
            $stmt->close();
        }

        $stmt_check->close();
        $conn->close();
    } else {
        $mensaje = "Todos los campos son obligatorios y el precio debe ser mayor o igual a 0.";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Juego</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'encabezado_nav.php'; ?>

    <div class="container">
        <h2>Agregar Nuevo Juego</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?= htmlspecialchars($tipo_mensaje) ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form class="review-form" method="POST" action="">
            <input type="text" name="nombre" placeholder="Nombre del juego" required>
            <textarea name="descripcion" placeholder="Descripción del juego" required></textarea>

            <select name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <option value="Acción">Acción</option>
                <option value="Aventura">Aventura</option>
                <option value="Estrategia">Estrategia</option>
                <option value="Deportes">Deportes</option>
            </select>

            <input type="number" name="precio" placeholder="Precio (ej. 59.99)" step="0.01" min="0" required>
            <input type="text" name="imagen" placeholder="URL de la imagen" required>
            <button type="submit">Agregar Juego</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Tienda de Videojuegos. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
