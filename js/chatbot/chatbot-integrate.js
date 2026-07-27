
// ============================================
// CHATBOT - INTÉGRATION AU SITE
// ============================================

function generateChatbotHTML() {
    const lang = getCurrentLanguage();
    const config = getChatbotLanguage(lang);
    
    // Vérifier si le widget existe déjà
    if (document.getElementById('chatbot-widget')) {
        return;
    }
    
    // Créer le widget
    const widget = document.createElement('div');
    widget.id = 'chatbot-widget';
    widget.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        display: none;
    `;
    
    // Bouton toggle
    const toggle = document.createElement('button');
    toggle.id = 'chatbot-toggle';
    toggle.textContent = config.buttonText;
    toggle.style.cssText = `
        background: #C9A84C;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        font-size: 22px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        color: #0A0A0A;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    toggle.onclick = toggleChatbot;
    
    // Conteneur du chat
    const container = document.createElement('div');
    container.id = 'chatbot-container';
    container.style.cssText = `
        display: none;
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 350px;
        height: 450px;
        border-radius: 15px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.2);
        background: #FFFFFF;
        overflow: hidden;
    `;
    
    // Iframe
    const iframe = document.createElement('iframe');
    iframe.id = 'chatbot-iframe';
    iframe.src = getChatbotUrl(lang);
    iframe.style.cssText = `
        width: 100%;
        height: 100%;
        border: none;
    `;
    iframe.allow = 'microphone';
    
    // Assembler
    container.appendChild(iframe);
    widget.appendChild(toggle);
    widget.appendChild(container);
    document.body.appendChild(widget);
}

// Générer le widget au chargement
document.addEventListener('DOMContentLoaded', function() {
    generateChatbotHTML();
});