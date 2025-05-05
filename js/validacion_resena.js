document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".review-form");

    if (form) {
        form.addEventListener("submit", function (e) {
            const comentario = form.querySelector("textarea[name='comentario']").value.trim();
            const juego = form.querySelector("select[name='juego_id']").value;
            const calificacion = form.querySelector("input[name='calificacion']:checked");

            if (!comentario || !juego || !calificacion) {
                e.preventDefault();
                alert("Por favor completa todos los campos correctamente.");
            }
        });
    }
});
