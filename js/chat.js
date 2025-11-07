document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button-chat');

    // ¡ADVERTENCIA DE SEGURIDAD!
    // Esta clave es visible para cualquiera que vea el código fuente de tu página.
    // Solo usa esta implementación para proyectos personales o de aprendizaje.
    const GROQ_API_KEY = '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';

    function addMessage(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');

        if (sender === 'bot') {
            messageElement.classList.add('bot-message');
            messageElement.innerHTML = `<strong>GameNexus Bot:</strong> ${message}`;
        } else {
            messageElement.classList.add('user-message');
            messageElement.innerHTML = `<strong>Tú:</strong> ${message}`;
        }

        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function sendMessage() {
        const userMessage = chatInput.value.trim();
        if (userMessage === '') return;

        addMessage('user', userMessage);
        chatInput.value = '';

        // Se envía la solicitud directamente a la API de Groq
        fetch('https://api.groq.com/openai/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${GROQ_API_KEY}`
            },
            body: JSON.stringify({
                model: 'llama3-8b-8192',
                messages: [
                    {
                        role: 'system',
                        content: 'Eres un asistente de inteligencia artificial para la tienda de videojuegos GameNexus. Responde preguntas de forma amigable y útil sobre juegos, consolas, géneros, horarios de la tienda, envíos y precios. Tu respuesta debe ser breve y concisa.'
                    },
                    {
                        role: 'user',
                        content: userMessage
                    }
                ]
            })
        })
        .then(response => {
            if (!response.ok) {
                // Si la respuesta no es OK, arroja un error con los datos de la respuesta.
                return response.json().then(errorData => {
                    const errorMessage = errorData.error ? errorData.error.message : 'Error desconocido de la API.';
                    throw new Error(`Error de la API: ${errorMessage} (Código: ${response.status})`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.choices && data.choices.length > 0 && data.choices[0].message) {
                const botResponse = data.choices[0].message.content;
                addMessage('bot', botResponse);
            } else {
                addMessage('bot', 'La API no devolvió una respuesta válida.');
            }
        })
        .catch(error => {
            console.error('Error en la solicitud de la API:', error);
            addMessage('bot', `Lo siento, no pude obtener una respuesta. Motivo: ${error.message}`);
        });
    }

    sendButton.addEventListener('click', sendMessage);

    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    addMessage('bot', '¡Hola! Soy el asistente de GameNexus. Pregúntame sobre cualquier videojuego o nuestra tienda.');
});