<?php
session_start();

if (isset($_POST['eliminar_id'])) {
    $id_eliminar = (int)$_POST['eliminar_id'];
    if (isset($_SESSION['carrito'][$id_eliminar])) {
        unset($_SESSION['carrito'][$id_eliminar]);
    }
}

if (isset($_POST['vaciar']) && $_POST['vaciar'] === 'true') {
    unset($_SESSION['carrito']);
}

$response = [
    'carrito' => $_SESSION['carrito'] ?? [],
    'total' => calcularTotal(),
    'html' => generarHtmlCarrito()
];

header('Content-Type: application/json');
echo json_encode($response);

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
    $html .= "</div>
    <div class='cart-actions'>
        <p><strong>Total: $<span id='total-carrito'>" . number_format(calcularTotal(), 2) . "</span></strong></p>
        <a href='#' id='vaciar-carrito' class='add-to-cart'>Vaciar Carrito</a>
        <a href='pagar.php' class='add-to-cart'>Pagar</a>
    </div>";

    return $html;
}

function calcularTotal() {
    return array_reduce($_SESSION['carrito'] ?? [], fn($total, $item) => $total + ($item['precio'] * $item['cantidad']), 0);
}
