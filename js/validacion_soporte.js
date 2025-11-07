document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.querySelector('.review-form');

    formulario.addEventListener('submit', function (e) {
        const nombre = formulario.nombre.value.trim();
        const email = formulario.email.value.trim();
        const motivo = formulario.motivo.value;
        const mensaje = formulario.mensaje.value.trim();

        let errores = [];

        if (nombre.length < 2) errores.push("El nombre es demasiado corto.");
        if (!/^\S+@\S+\.\S+$/.test(email)) errores.push("El correo electrónico no es válido.");
        if (!motivo) errores.push("Debes seleccionar un motivo.");
        if (mensaje.length < 10) errores.push("El mensaje debe tener al menos 10 caracteres.");

        if (errores.length > 0) {
            e.preventDefault();
            alert("Errores:\n" + errores.join("\n"));
        }
    });
});
