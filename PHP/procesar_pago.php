<?php
session_start();
include 'conexion.php';
// --- NUEVO: Incluye el script de ayuda para otorgar logros ---
include 'otorgar_logro.php';

// Función para generar un código de 25 caracteres con el formato deseado
function generarCodigoUnico() {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo_final = '';
    // Generar 5 grupos de 5 caracteres
    for ($j = 0; $j < 5; $j++) {
        $grupo = '';
        for ($i = 0; $i < 5; $i++) {
            $grupo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        $codigo_final .= $grupo;
        if ($j < 4) {
            $codigo_final .= '-';
        }
    }
    return $codigo_final;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../HTML/pagar.php");
    exit();
}

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito']) || !isset($_SESSION['usuario_id'])) {
    header("Location: ../HTML/carrito.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Función de limpieza de datos
function limpiar($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

// Limpieza y validación de datos
$nombre = limpiar($_POST['nombre'] ?? '');
$email = limpiar($_POST['email'] ?? '');
$telefono = limpiar($_POST['telefono'] ?? '');
$calle = limpiar($_POST['calle'] ?? '');
$ciudad = limpiar($_POST['ciudad'] ?? '');
$estado = limpiar($_POST['estado'] ?? '');
$codigo_postal = limpiar($_POST['codigo_postal'] ?? '');
$pais = limpiar($_POST['pais'] ?? '');
$metodo_pago = limpiar($_POST['metodo_pago'] ?? '');
$numero_tarjeta = limpiar($_POST['numero_tarjeta'] ?? '');
$fecha_expiracion = limpiar($_POST['fecha_expiracion'] ?? '');
$cvv = limpiar($_POST['cvv'] ?? '');
$errores = [];

if (!$nombre) $errores[] = "El nombre es obligatorio.";
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido.";
if (!$telefono || !preg_match('/^[0-9]{7,15}$/', $telefono)) $errores[] = "El teléfono no es válido.";
if (!$calle) $errores[] = "La calle es obligatoria.";
if (!$ciudad) $errores[] = "La ciudad es obligatoria.";
if (!$estado) $errores[] = "El estado/provincia es obligatorio.";
if (!$codigo_postal || !preg_match('/^[0-9]{4,10}$/', $codigo_postal)) $errores[] = "El código postal no es válido.";
if (!$pais) $errores[] = "El país es obligatorio.";

if ($metodo_pago !== 'tarjeta' && $metodo_pago !== 'paypal') {
    $errores[] = "Método de pago inválido.";
}
if ($metodo_pago === 'tarjeta') {
    if (!$numero_tarjeta || !preg_match('/^\d{13,19}$/', $numero_tarjeta)) {
        $errores[] = "Número de tarjeta inválido.";
    }
    if (!$fecha_expiracion) {
        $errores[] = "Fecha de expiración es obligatoria.";
    }
    if (!$cvv || !preg_match('/^\d{3,4}$/', $cvv)) {
        $errores[] = "CVV inválido.";
    }
}
if (!empty($errores)) {
    $_SESSION['errores_pago'] = $errores;
    header("Location: ../HTML/pagar.php");
    exit();
}

$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

$conn->begin_transaction();
$pago_exitoso = true;
$juegos_comprados_con_qr = [];

try {
    $stmt = $conn->prepare("INSERT INTO compras (usuario_id, juego_id, codigo_qr) VALUES (?, ?, ?)");
    foreach ($_SESSION['carrito'] as $juego_id => $item) {
        $codigo_qr = generarCodigoUnico();
        $stmt->bind_param("iis", $usuario_id, $juego_id, $codigo_qr);
        if (!$stmt->execute()) {
            $pago_exitoso = false;
            break;
        }
        $juegos_comprados_con_qr[] = [
            'nombre' => $item['nombre'],
            'codigo_qr' => $codigo_qr
        ];
    }

    if ($pago_exitoso) {
        $conn->commit();
        
        // --- INICIO DE LA LÓGICA DE LOGROS (ACTUALIZADA) ---
        
        // Logro 1: Comprador novato (Primera compra)
        otorgar_logro($conn, $usuario_id, 'primera_compra');
        
        // Logro 2: Coleccionista Amateur (5 juegos)
        $stmt_compras_total = $conn->prepare("SELECT COUNT(*) AS total FROM compras WHERE usuario_id = ?");
        $stmt_compras_total->bind_param("i", $usuario_id);
        $stmt_compras_total->execute();
        $total_compras = $stmt_compras_total->get_result()->fetch_assoc()['total'];
        if ($total_compras >= 5) {
            otorgar_logro($conn, $usuario_id, 'cinco_compras');
        }
        $stmt_compras_total->close();

        // Logro 3: Explorador de Géneros (3 géneros diferentes)
        $stmt_generos = $conn->prepare("SELECT COUNT(DISTINCT j.categoria) AS total_generos FROM compras c JOIN juegos j ON c.juego_id = j.id WHERE c.usuario_id = ?");
        $stmt_generos->bind_param("i", $usuario_id);
        $stmt_generos->execute();
        $total_generos = $stmt_generos->get_result()->fetch_assoc()['total_generos'];
        if ($total_generos >= 3) {
            otorgar_logro($conn, $usuario_id, 'tres_generos');
        }
        $stmt_generos->close();

        // Logro 4: Cliente Fiel (3 meses diferentes)
        $stmt_meses = $conn->prepare("SELECT COUNT(DISTINCT MONTH(fecha_compra)) AS meses FROM compras WHERE usuario_id = ?");
        $stmt_meses->bind_param("i", $usuario_id);
        $stmt_meses->execute();
        $total_meses = $stmt_meses->get_result()->fetch_assoc()['meses'];
        if ($total_meses >= 3) {
            otorgar_logro($conn, $usuario_id, 'cliente_fiel');
        }
        $stmt_meses->close();
        
        // --- FIN DE LA LÓGICA DE LOGROS ---
        
        $_SESSION['orden'] = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'direccion' => [
                'calle' => $calle,
                'ciudad' => $ciudad,
                'estado' => $estado,
                'codigo_postal' => $codigo_postal,
                'pais' => $pais,
            ],
            'metodo_pago' => $metodo_pago,
            'total' => $total,
            'juegos_comprados_con_qr' => $juegos_comprados_con_qr,
        ];
        unset($_SESSION['carrito']);
        header('Location: ../confirmacion_pago.php');
    } else {
        $conn->rollback();
        $_SESSION['mensaje_error'] = "Error al procesar la compra. Inténtalo de nuevo.";
        header('Location: ../HTML/pagar.php');
    }
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['mensaje_error'] = "Ocurrió un error inesperado: " . $e->getMessage();
    header('Location: ../HTML/pagar.php');
}
$stmt->close();
$conn->close();
exit();
?>