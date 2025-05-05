document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".review-form");

    if (form) {
        form.addEventListener("submit", function (e) {
            const nombre = form.querySelector("input[name='nombre']").value.trim();
            const descripcion = form.querySelector("textarea[name='descripcion']").value.trim();
            const categoria = form.querySelector("select[name='categoria']").value;
            const precio = parseFloat(form.querySelector("input[name='precio']").value);
            const imagen = form.querySelector("input[name='imagen']").value.trim();

            let errores = [];

            if (!nombre) errores.push("El nombre es obligatorio.");
            if (!descripcion) errores.push("La descripción es obligatoria.");
            if (!categoria) errores.push("Selecciona una categoría.");
            if (isNaN(precio) || precio < 0) errores.push("El precio debe ser un número mayor o igual a 0.");
            if (!imagen || !/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp)$/i.test(imagen)) {
                errores.push("La URL de la imagen no es válida.");
            }

            if (errores.length > 0) {
                e.preventDefault();
                alert(errores.join("\n"));
            }
        });
    }
});
