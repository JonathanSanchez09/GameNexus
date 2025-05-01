document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".register-form");
  const registerButton = form.querySelector("button");

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

    // Validación de correo electrónico
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      mostrarMensaje("El correo electrónico no es válido.", "red");
      return;
    }

    // Validación de la contraseña (mínimo 8 caracteres, al menos un número, una letra mayúscula y un carácter especial)
    const passwordRegex = /^(?=.*\d)(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}$/;
    if (!passwordRegex.test(password)) {
      mostrarMensaje("La contraseña debe tener al menos 8 caracteres, una letra mayúscula, un número y un carácter especial.", "red");
      return;
    }

    if (password !== confirmPassword) {
      mostrarMensaje("Las contraseñas no coinciden.", "red");
      return;
    }

    // Deshabilitar el botón para evitar envíos múltiples
    registerButton.disabled = true;

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
        // Limpiar los campos después del registro exitoso
        form.reset();
        setTimeout(() => {
          window.location.href = "./login.html";  // Redirigir al login
        }, 2000);
      } else {
        // Rehabilitar el botón si hay un error
        registerButton.disabled = false;
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
