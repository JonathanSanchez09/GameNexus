document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("login-form");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;

    if (!email || !password) {
      mostrarMensaje("Todos los campos son obligatorios.", "red");
      return;
    }

    console.log("Enviando login para:", email);

    fetch("php/login.php", {
      method: "POST",
      body: new URLSearchParams({ email, password }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("HTTP error " + response.status);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Respuesta del backend:", data);
        const existingErrorDiv = document.querySelector(".input-error");
        if (existingErrorDiv) existingErrorDiv.remove();

        if (data.success) {
          window.location.href = "index.php";
        } else {
          mostrarMensaje(data.message, "red");
        }
      })
      .catch((error) => {
        mostrarMensaje("Error de conexión: " + error.message, "red");
        console.error("Fetch error:", error);
      });
  });

  function mostrarMensaje(texto, color) {
    const errorContainer = document.querySelector(".login-container");
    const errorDiv = document.createElement("div");
    errorDiv.className = "input-error";
    errorDiv.style.textAlign = "center";
    errorDiv.style.color = color;
    errorDiv.textContent = `❌ ${texto}`;
    errorContainer.prepend(errorDiv);
  }
});
