document.addEventListener("DOMContentLoaded", function() {
    // Manejar clics para eliminar y vaciar el carrito
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("remove-item")) {
            event.preventDefault();
            actualizarCarrito("eliminar_id", event.target.getAttribute("data-id"));
        } else if (event.target.id === "vaciar-carrito") {
            event.preventDefault();
            actualizarCarrito("vaciar", "true");
        }
    });
});

// Función general para actualizar el carrito
function actualizarCarrito(tipo, valor) {
    const data = new FormData();
    data.append(tipo, valor);

    fetch('../PHP/actualizar_carrito.php', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.html) {
            actualizarVistaCarrito(data);
            mostrarMensaje(tipo === "vaciar" ? "✅ Carrito vaciado correctamente." : "✅ Juego eliminado del carrito.", "green");
        } else {
            mostrarMensaje("❌ Ocurrió un error al actualizar el carrito.", "red");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje("❌ Error de conexión.", "red");
    });
}

// Actualizar la vista del carrito
function actualizarVistaCarrito(data) {
    const carritoContenido = document.getElementById("carrito-contenido");
    const totalCarrito = document.getElementById("total-carrito");

    carritoContenido.innerHTML = data.html;
    totalCarrito.textContent = '$' + data.total.toFixed(2);
}

// Mostrar mensaje de confirmación o error
function mostrarMensaje(texto, color) {
    const container = document.querySelector(".container");
    const msg = document.createElement("div");
    msg.className = "alerta-mensaje";
    msg.style.color = color;
    msg.textContent = texto;
    container.prepend(msg);

    setTimeout(() => {
        msg.remove();
    }, 3000);
}
