document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".register-form");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    const container = document.querySelector(".register-container");
    const existingError = document.querySelector(".input-error");
    if (existingError) existingError.remove();

    // Validaciones básicas
    if (!username || !email || !password || !confirmPassword) {
      mostrarMensaje("Todos los campos son obligatorios.", "red");
      return;
    }

    if (password.length < 8) {
      mostrarMensaje("La contraseña debe tener al menos 8 caracteres.", "red");
      return;
    }

    if (password !== confirmPassword) {
      mostrarMensaje("Las contraseñas no coinciden.", "red");
      return;
    }

    // Envío con fetch
    fetch("../PHP/registrar_usuario.php", {
      method: "POST",
      body: new URLSearchParams({
        username,
        email,
        password
      })
    })
    .then(res => res.json())
    .then(data => {
      mostrarMensaje(data.message, data.success ? "green" : "red");

      if (data.success) {
        setTimeout(() => {
          window.location.href = "./login.html";
        }, 2000);
      }
    });

    function mostrarMensaje(texto, color) {
      const msg = document.createElement("div");
      msg.className = "input-error";
      msg.style.textAlign = "center";
      msg.style.color = color;
      msg.textContent = (color === "green" ? "✅ " : "❌ ") + texto;
      container.prepend(msg);
    }
  });
});
