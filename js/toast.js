function mostrarToast(mensaje) {
    const toast = document.createElement('div');
    toast.textContent = mensaje;
    Object.assign(toast.style, {
        position: "fixed",
        bottom: "20px",
        right: "20px",
        backgroundColor: "#198754",
        color: "#fff",
        padding: "15px 20px",
        borderRadius: "8px",
        boxShadow: "0 4px 8px rgba(0,0,0,0.3)",
        fontFamily: "'Poppins', sans-serif",
        zIndex: 1000,
        fontSize: "16px"
    });
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
