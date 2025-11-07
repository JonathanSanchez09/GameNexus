document.addEventListener("DOMContentLoaded", function() {
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

function actualizarCarrito(tipo, valor) {
    const data = new FormData();
    data.append(tipo, valor);

    fetch('/php/actualizar_carrito.php', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Para depurar

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

function actualizarVistaCarrito(data) {
    const carritoContenido = document.getElementById("carrito-contenido");
    const totalCarrito = document.getElementById("total-carrito");
    const botonVaciar = document.getElementById("vaciar-carrito");
    const botonPagar = document.querySelector("a[href='pagar.php']");
    const cartCount = document.getElementById("cart-count");

    if (carritoContenido) carritoContenido.innerHTML = data.html;

    if (totalCarrito) totalCarrito.textContent = '$' + data.total.toFixed(2);

    if (botonVaciar) botonVaciar.style.pointerEvents = (data.total === 0) ? 'none' : 'auto';
    if (botonVaciar) botonVaciar.style.opacity = (data.total === 0) ? 0.5 : 1;
    if (botonPagar) botonPagar.style.pointerEvents = (data.total === 0) ? 'none' : 'auto';
    if (botonPagar) botonPagar.style.opacity = (data.total === 0) ? 0.5 : 1;

    if (cartCount) {
        const totalItems = data.carrito ? Object.values(data.carrito).reduce((acc, item) => acc + item.cantidad, 0) : 0;
        cartCount.textContent = totalItems;
    }
}

function mostrarMensaje(texto, color) {
    const container = document.querySelector(".container");
    if (!container) return;

    const msg = document.createElement("div");
    msg.className = "alerta-mensaje";
    msg.style.color = color;
    msg.textContent = texto;
    container.prepend(msg);

    setTimeout(() => {
        msg.remove();
    }, 3000);
}
