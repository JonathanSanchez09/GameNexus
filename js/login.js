document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("login-form");
  
  form.addEventListener("submit", (event) => {
    event.preventDefault(); // Esto evita que el formulario se envíe
    
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    
    // Aquí puedes realizar la validación y envío usando fetch
    fetch("../PHP/login.php", {
      method: "POST",
      body: new URLSearchParams({
        email: email,
        password: password
      })
    })
    .then(response => response.json())
    .then(data => {
      const errorContainer = document.querySelector(".login-container");
      const existingErrorDiv = document.querySelector(".input-error");
      if (existingErrorDiv) {
        existingErrorDiv.remove(); // Eliminar mensaje de error previo
      }
      if (data.success) {
        window.location.href = "../html/index.php";  // Redirige si el login es exitoso
      } else {
        // Muestra el error
        const errorDiv = document.createElement("div");
        errorDiv.className = "input-error";
        errorDiv.style.textAlign = "center";
        errorDiv.style.color = "red"; 
        errorDiv.textContent = `❌ ${data.message}`;
        errorContainer.prepend(errorDiv);
      }
    });
  });
});
