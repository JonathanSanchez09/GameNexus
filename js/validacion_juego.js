document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".review-form");

    if (form) {
        form.addEventListener("submit", function (e) {
            // ... (código existente de captura de variables) ...
            const nombre = form.querySelector("input[name='nombre']").value.trim();
            const descripcion = form.querySelector("textarea[name='descripcion']").value.trim();
            const categoria = form.querySelector("select[name='categoria']").value;
            const precio = parseFloat(form.querySelector("input[name='precio']").value);
            const imagen = form.querySelector("input[name='imagen']").value.trim();

            let errores = [];

            if (!nombre) errores.push("El nombre es obligatorio.");
            if (!descripcion) errores.push("La descripción es obligatoria.");
            
            // ✅ Validación de Categoría: Asegura que se seleccione una opción no vacía
            if (!categoria) errores.push("Selecciona una categoría.");
            
            if (isNaN(precio) || precio < 0) errores.push("El precio debe ser un número mayor o igual a 0.");
            
            // Mejora en la validación de URL para ser más permisiva pero aún útil
            if (!imagen || !/^https?:\/\/.+/i.test(imagen)) {
                 errores.push("La URL de la imagen no es válida o está vacía.");
            }

            if (errores.length > 0) {
                e.preventDefault();
                alert("Errores de validación:\n" + errores.join("\n"));
            }
        });
    }
});