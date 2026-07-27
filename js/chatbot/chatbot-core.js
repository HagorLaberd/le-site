
// ============================================
// CHATBOT - CORE
// ============================================

// Détecter la langue actuelle
function getCurrentLanguage() {
    const path = window.location.pathname;
    const langCodes = ['ar', 'fr', 'id', 'ms', 'zh'];
    for (let lang of langCodes) {
        if (path.includes('/' + lang + '/')) {
            return lang;
        }
    }
    return 'en';
}

// Construire l'URL du chatbot
function getChatbotUrl(lang) {
    const baseUrl = 'http://localhost:5005/webchat';
    const sessionId = 'session_' + Math.random().toString(36).substr(2, 9);
    return baseUrl + '/?session_id=' + sessionId;
}

// Initialiser le chatbot
function initChatbot() {
    const lang = getCurrentLanguage();
    const config = getChatbotLanguage(lang);
    
    const widget = document.getElementById('chatbot-widget');
    const toggle = document.getElementById('chatbot-toggle');
    const container = document.getElementById('chatbot-container');
    const iframe = document.getElementById('chatbot-iframe');
    
    if (toggle) {
        toggle.textContent = config.buttonText;
    }
    
    if (iframe) {
        iframe.src = getChatbotUrl(lang);
    }
    
    // Afficher le widget après 3 secondes
    setTimeout(function() {
        if (widget) {
            widget.style.display = 'block';
        }
    }, 3000);
}

// Basculer l'ouverture/fermeture du chatbot
function toggleChatbot() {
    const container = document.getElementById('chatbot-container');
    const toggle = document.getElementById('chatbot-toggle');
    
    if (!container) return;
    
    if (container.style.display === 'none' || container.style.display === '') {
        container.style.display = 'block';
        if (toggle) {
            toggle.textContent = '✕';
            toggle.style.background = '#1B5E20';
        }
    } else {
        container.style.display = 'none';
        if (toggle) {
            const lang = getCurrentLanguage();
            const config = getChatbotLanguage(lang);
            toggle.textContent = config.buttonText;
            toggle.style.background = '#C9A84C';
        }
    }
}

// Redimensionner le chatbot sur mobile
function resizeChatbot() {
    const container = document.getElementById('chatbot-container');
    if (!container) return;
    
    if (window.innerWidth < 576) {
        container.style.width = '90vw';
        container.style.height = '70vh';
        container.style.right = '5vw';
    } else {
        container.style.width = '350px';
        container.style.height = '450px';
        container.style.right = '0';
    }
}

// Exposer les fonctions globalement
window.initChatbot = initChatbot;
window.toggleChatbot = toggleChatbot;
window.getCurrentLanguage = getCurrentLanguage;
window.resizeChatbot = resizeChatbot;

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initChatbot, 500);
    window.addEventListener('resize', resizeChatbot);
    resizeChatbot();
});