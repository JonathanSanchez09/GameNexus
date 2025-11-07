document.addEventListener('DOMContentLoaded', function() {
    const metodoPagoSelect = document.getElementById('metodo_pago');
    const datosTarjetaDiv = document.getElementById('datos_tarjeta');

    metodoPagoSelect.addEventListener('change', function() {
        if (this.value === 'tarjeta') {
            datosTarjetaDiv.style.display = 'block';
            datosTarjetaDiv.querySelectorAll('input').forEach(input => {
                input.required = true;
            });
        } else {
            datosTarjetaDiv.style.display = 'none';
            datosTarjetaDiv.querySelectorAll('input').forEach(input => {
                input.required = false;
            });
        }
    });
});
