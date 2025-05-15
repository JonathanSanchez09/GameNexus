<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../HTML/pagar.php");
    exit();
}

// Validar que el carrito tenga productos
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: ../HTML/carrito.php");
    exit();
}

// Función para limpiar datos recibidos
function limpiar($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

// Recibir y limpiar datos
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

// Validaciones básicas
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

// Si método tarjeta, validar datos de tarjeta
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
    // Si hay errores, los mostramos y volvemos al formulario (se puede mejorar)
    echo "<div class='container'>";
    echo "<h2>Errores en el pago</h2>";
    echo "<ul>";
    foreach ($errores as $error) {
        echo "<li style='color:#ff5555;'>$error</li>";
    }
    echo "</ul>";
    echo "<a href='../HTML/pagar.php'>Volver al formulario</a>";
    echo "</div>";
    exit();
}

// Aquí iría la lógica real de procesamiento del pago:
// - Integrar con pasarelas de pago (PayPal, Stripe, etc.)
// - Guardar la orden en base de datos
// - Enviar correo de confirmación, etc.

// Como ejemplo, simulamos éxito:

// Guardar datos de la orden en sesión para mostrar en confirmación, por ejemplo
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
    'total' => 0,
];

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
$_SESSION['orden']['total'] = $total;

// Vaciar carrito (opcional, porque ya se pagó)
unset($_SESSION['carrito']);

// Redirigir a página de confirmación
header("Location: ../HTML/confirmacion_pago.php");
exit();
