document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");
  
    if (error) {
      const errorDiv = document.createElement("div");
      errorDiv.className = "input-error";
      errorDiv.style.textAlign = "center";
      errorDiv.style.color = "red";  // Agrega color rojo al mensaje
  
      switch (error) {
        case "usuario_no_existe":
          errorDiv.textContent = "❌ El usuario no existe.";
          break;
        case "contraseña_incorrecta":
          errorDiv.textContent = "❌ Contraseña incorrecta.";
          break;
        default:
          errorDiv.textContent = "❌ Ocurrió un error inesperado.";
      }
  
      document.querySelector(".login-container").prepend(errorDiv);
    }
  });
  