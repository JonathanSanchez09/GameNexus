// Validación rápida en el navegador antes de enviar
document.getElementById('registroForm').addEventListener('submit', function(event) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm-password').value;
  
    if (password !== confirm) {
      alert('❌ Las contraseñas no coinciden.');
      event.preventDefault(); // Detiene el envío del formulario
    }
  });
  