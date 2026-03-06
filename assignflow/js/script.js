document.addEventListener('DOMContentLoaded', () => {
    // Chatbot Toggle
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotContainer = document.getElementById('chatbot-container');
    const closeChat = document.getElementById('close-chat');

    if (chatbotToggle) {
        chatbotToggle.addEventListener('click', () => {
            chatbotContainer.style.display = 'flex';
            chatbotToggle.style.display = 'none';
        });
    }

    if (closeChat) {
        closeChat.addEventListener('click', () => {
            chatbotContainer.style.display = 'none';
            chatbotToggle.style.display = 'flex';
        });
    }
});

// Helper for Mock AI Chat
function sendMessage() {
    const input = document.getElementById('chat-input-field');
    const messages = document.getElementById('chatbot-messages');
    const text = input.value.trim();

    if (text) {
        // User Message
        const userMsg = document.createElement('div');
        userMsg.className = 'chat-msg user';
        userMsg.textContent = text;
        messages.appendChild(userMsg);

        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        // Bot Response (Mock)
        setTimeout(() => {
            const botMsg = document.createElement('div');
            botMsg.className = 'chat-msg bot';
            botMsg.textContent = "I'm a simple AI helper. I can't answer complex queries yet, but I'm here to assist!";
            messages.appendChild(botMsg);
            messages.scrollTop = messages.scrollHeight;
        }, 1000);
    }
}
