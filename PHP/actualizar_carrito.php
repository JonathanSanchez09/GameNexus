<?php
session_start();
include '../PHP/conexion.php';

// Verificar si se está enviando una solicitud para eliminar un juego del carrito
if (isset($_POST['eliminar_id'])) {
    $id_eliminar = (int)$_POST['eliminar_id'];

    if (isset($_SESSION['carrito'][$id_eliminar])) {
        unset($_SESSION['carrito'][$id_eliminar]);
    }
}


// Verificar si se está enviando una solicitud para vaciar el carrito
if (isset($_POST['vaciar']) && $_POST['vaciar'] === 'true') {
    unset($_SESSION['carrito']);
}

// Generar el HTML actualizado del carrito
$html = generarHtmlCarrito();

// Devolver el carrito actualizado como respuesta JSON
$response = [
    'carrito' => $_SESSION['carrito'] ?? [],
    'total' => calcularTotal(),
    'html' => $html
];

header('Content-Type: application/json');
echo json_encode($response);

// Función para generar el HTML del carrito
function generarHtmlCarrito() {
    if (empty($_SESSION['carrito'])) return '<p>No hay juegos en tu carrito.</p>';

    $html = "<div class='games'>";

    foreach ($_SESSION['carrito'] as $id => $item) {
        $html .= "<div class='game' data-id='$id'>
                    <img src='" . htmlspecialchars($item['imagen']) . "' alt='" . htmlspecialchars($item['nombre']) . "'>
                    <h2>" . htmlspecialchars($item['nombre']) . "</h2>
                    <p><strong>Precio:</strong> $" . number_format($item['precio'], 2) . "</p>
                    <p><strong>Cantidad:</strong> " . $item['cantidad'] . "</p>
                    <a href='#' class='remove-item add-to-cart' data-id='$id'>❌ Eliminar</a>
                  </div>";
    }

    $html .= "</div>";
    return $html;
}

// Función para calcular el total del carrito
function calcularTotal() {
    return array_reduce($_SESSION['carrito'] ?? [], fn($total, $item) => $total + ($item['precio'] * $item['cantidad']), 0);
}
?>
