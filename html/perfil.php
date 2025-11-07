<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';
include './php/conexion.php';

// Redirige si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener la información completa del usuario
$stmt_usuario = $conn->prepare("SELECT email, nombre_completo, fecha_nacimiento, direccion FROM usuarios WHERE id = ?");
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$resultado_usuario = $stmt_usuario->get_result();
$usuario = $resultado_usuario->fetch_assoc();

// Obtener las reseñas del usuario
$stmt_resenas = $conn->prepare("
    SELECT r.comentario, r.calificacion, j.nombre AS nombre_juego, j.id AS juego_id
    FROM resenas r
    JOIN juegos j ON r.juego_id = j.id
    WHERE r.usuario_id = ?
    ORDER BY r.fecha DESC
");
$stmt_resenas->bind_param("i", $usuario_id);
$stmt_resenas->execute();
$resenas = $stmt_resenas->get_result();

// Obtener el historial de compras del usuario
$stmt_compras = $conn->prepare("
    SELECT j.nombre, j.id AS juego_id, j.imagen_url, c.codigo_qr
    FROM compras c
    JOIN juegos j ON c.juego_id = j.id
    WHERE c.usuario_id = ?
    ORDER BY c.fecha_compra DESC
");
$stmt_compras->bind_param("i", $usuario_id);
$stmt_compras->execute();
$compras = $stmt_compras->get_result();

// --- Obtener los logros del usuario ---
$stmt_logros = $conn->prepare("
    SELECT l.nombre, l.descripcion, l.imagen_url
    FROM usuario_logros ul
    JOIN logros l ON ul.logro_id = l.id
    WHERE ul.usuario_id = ?
");
$stmt_logros->bind_param("i", $usuario_id);
$stmt_logros->execute();
$logros = $stmt_logros->get_result();
?>

<div class="container">
    <h1 class="section-title" style="text-align:center;">Perfil de Usuario</h1>

    <div class="user-profile">
        <div class="user-info">
            <h2>Información General</h2>
            
            <form action="php/actualizar_perfil.php" method="POST">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($usuario['nombre_completo'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['fecha_nacimiento'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Información</button>
            </form>
        </div>
        
        <hr style="border: 0; border-top: 1px solid #00ffcc; margin: 30px 0;">

        <div class="user-achievements">
            <h2>Mis Logros 🏅</h2>
            <?php if ($logros->num_rows > 0): ?>
                <div class="logros-grid" style="display: flex; flex-wrap: nowrap; overflow-x: auto; gap: 20px; padding: 10px;">
                    <?php while ($logro = $logros->fetch_assoc()): ?>
                        <div class="logro-item" style="flex: 0 0 auto; text-align: center; width: 120px;">
                            <!-- CAMBIO: Se elimina el <p> de la descripción y se añade al atributo title de la imagen -->
                            <img src="<?php echo htmlspecialchars($logro['imagen_url']); ?>" alt="<?php echo htmlspecialchars($logro['nombre']); ?>" title="<?php echo htmlspecialchars($logro['descripcion']); ?>">
                            <p><strong><?php echo htmlspecialchars($logro['nombre']); ?></strong></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Aún no has obtenido ningún logro. ¡Completa tu primera compra o reseña para empezar!</p>
            <?php endif; ?>
        </div>

        <hr style="border: 0; border-top: 1px solid #00ffcc; margin: 30px 0;">

        <div class="user-reviews">
            <h2>Mis Reseñas</h2>
            <?php if ($resenas->num_rows > 0): ?>
                <?php while ($resena = $resenas->fetch_assoc()): ?>
                    <div class="review-item">
                        <h3>En el juego: <a href="recomendaciones.php?juego_id=<?php echo htmlspecialchars($resena['juego_id']); ?>"><?php echo htmlspecialchars($resena['nombre_juego']); ?></a></h3>
                        <p class="rating"><?php echo str_repeat('⭐', $resena['calificacion']) . str_repeat('☆', 5 - $resena['calificacion']); ?></p>
                        <p><?php echo htmlspecialchars($resena['comentario']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aún no has escrito ninguna reseña.</p>
            <?php endif; ?>
        </div>

        <hr style="border: 0; border-top: 1px solid #00ffcc; margin: 30px 0;">

        <div class="purchase-history">
            <h2>Historial de Compras</h2>
            <div class="games">
                <?php if ($compras->num_rows > 0): ?>
                    <?php while ($compra = $compras->fetch_assoc()): ?>
                        <div class="game">
                            <img src="<?php echo htmlspecialchars($compra['imagen_url']); ?>" alt="<?php echo htmlspecialchars($compra['nombre']); ?>">
                            <h3><?php echo htmlspecialchars($compra['nombre']); ?></h3>
                            <span class="codigo-compra" style="font-size: 0.9em;"><?php echo htmlspecialchars($compra['codigo_qr']); ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Aún no has realizado ninguna compra.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt_usuario->close();
$stmt_resenas->close();
$stmt_compras->close();
$stmt_logros->close();
$conn->close();
include 'footer.php'; 
?>
