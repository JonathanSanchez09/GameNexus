<?php
// ====================================================================
// PASO 1: INICIAR SESIÓN Y PROCESAR LÓGICA (¡DEBE SER LO PRIMERO!)
// ====================================================================
session_start();

// Incluimos la conexión a la base de datos (asumiendo que inicializa $conn)
// Se incluye aquí la conexión para que esté disponible si el POST tiene éxito.
// NOTA: Si 'header.php' incluye HTML, debemos moverlo después de este bloque.
// Asumo que 'conexion.php' NO tiene salida HTML.
include('./php/conexion.php');

$mensaje = "";
$tipo_mensaje = ""; 
$form_data = []; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 🛡️ PROTECCIÓN XSS (ENTRADA): Sanitización de todas las variables de texto
    $nombre = trim(htmlspecialchars($_POST['nombre'] ?? '', ENT_QUOTES, 'UTF-8'));
    $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'));
    $categoria = trim(htmlspecialchars($_POST['categoria'] ?? '', ENT_QUOTES, 'UTF-8'));
    $imagen = trim(htmlspecialchars($_POST['imagen'] ?? '', ENT_QUOTES, 'UTF-8'));
    $precio = (float) ($_POST['precio'] ?? 0.0);
    
    // Guardar datos en caso de error de validación
    $form_data = $_POST;

    // Validación de datos mínimos
    if (empty($nombre) || empty($descripcion) || empty($categoria) || $precio < 0 || empty($imagen)) {
        $mensaje = "Todos los campos son obligatorios y el precio debe ser mayor o igual a 0.";
        $tipo_mensaje = "error";
    } else {
        // ✅ Protección SQLi: Comprobar existencia (Sentencia Preparada)
        $stmt_check = $conn->prepare("SELECT id FROM juegos WHERE nombre = ?");
        $stmt_check->bind_param("s", $nombre);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $mensaje = "Ya existe un juego con ese nombre.";
            $tipo_mensaje = "error";
        } else {
            // ✅ Protección SQLi: Inserción (Sentencia Preparada)
            $slug = strtolower(str_replace(' ', '-', $nombre)); 
            
            // Asumo que tu tabla tiene 'slug' y 'fecha_lanzamiento'
            $stmt = $conn->prepare("INSERT INTO juegos (nombre, descripcion, categoria, precio, imagen_url, slug, fecha_lanzamiento) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            
            // La "s" extra al final es para el 'slug' que se añadió
            $stmt->bind_param("sssds", $nombre, $descripcion, $categoria, $precio, $imagen, $slug); 

            if ($stmt->execute()) {
                $mensaje = "Juego agregado correctamente.";
                
                // 🛑 CORRECCIÓN DE ERROR DE CABECERA: La redirección ocurre antes de cualquier HTML
                header("Location: index.php?mensaje=" . urlencode($mensaje));
                exit();
            } else {
                $mensaje = "Error al agregar el juego. Por favor, revisa la conexión o los datos.";
                $tipo_mensaje = "error";
            }
            $stmt->close();
        }

        $stmt_check->close();
    }
}

// ====================================================================
// PASO 2: INCLUIR HTML Y MOSTRAR FORMULARIO (Despues de la Lógica POST)
// ====================================================================
include 'header.php';
include 'encabezado_nav.php';
?>

    <div class="container">
        <h2>Agregar Nuevo Juego</h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alerta-mensaje <?= $tipo_mensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form class="review-form" method="POST" action="">
            <input type="text" name="nombre" placeholder="Nombre del juego" 
                   value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>" required>
                   
            <textarea name="descripcion" placeholder="Descripción del juego" required><?= htmlspecialchars($form_data['descripcion'] ?? '') ?></textarea>

            <select name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php $cat_sel = $form_data['categoria'] ?? ''; ?>
                <option value="Accion" <?= ($cat_sel == 'Accion' ? 'selected' : '') ?>>Acción</option>
                <option value="Aventura" <?= ($cat_sel == 'Aventura' ? 'selected' : '') ?>>Aventura</option>
                <option value="Estrategia" <?= ($cat_sel == 'Estrategia' ? 'selected' : '') ?>>Estrategia</option>
                <option value="Deportes" <?= ($cat_sel == 'Deportes' ? 'selected' : '') ?>>Deportes</option>
                </select>

            <input type="number" name="precio" placeholder="Precio (ej. 59.99)" step="0.01" min="0" required
                   value="<?= htmlspecialchars($form_data['precio'] ?? '') ?>">
            
            <input type="text" name="imagen" placeholder="URL de la imagen" required
                   value="<?= htmlspecialchars($form_data['imagen'] ?? '') ?>">
                   
            <button type="submit">Agregar Juego</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>