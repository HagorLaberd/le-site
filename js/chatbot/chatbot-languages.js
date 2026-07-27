
// ============================================
// CHATBOT - CONFIGURATION DES LANGUES
// ============================================

const CHATBOT_LANGUAGES = {
    // Langues actives
    en: {
        code: 'en',
        name: 'English',
        locale: 'en',
        dir: 'ltr',
        placeholder: 'Ask me anything...',
        welcome: 'Welcome! How can I help you today?',
        quickReplies: ['Services', 'Consultation', 'Contact'],
        buttonText: '💬 Chat',
        widgetTitle: 'Chat with us'
    },
    ar: {
        code: 'ar',
        name: 'العربية',
        locale: 'ar',
        dir: 'ltr',
        placeholder: 'اطرح أي سؤال...',
        welcome: 'مرحباً! كيف يمكنني مساعدتك اليوم؟',
        quickReplies: ['الخدمات', 'استشارة', 'اتصل بنا'],
        buttonText: '💬 محادثة',
        widgetTitle: 'تحدث معنا'
    },
    fr: {
        code: 'fr',
        name: 'Français',
        locale: 'fr',
        dir: 'ltr',
        placeholder: 'Posez-moi une question...',
        welcome: 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?',
        quickReplies: ['Services', 'Consultation', 'Contact'],
        buttonText: '💬 Chat',
        widgetTitle: 'Discutons'
    },
    
    // 🔮 LANGUES FUTURES (préparées pour plus tard)
    id: {
        code: 'id',
        name: 'Bahasa Indonesia',
        locale: 'id',
        dir: 'ltr',
        placeholder: 'Tanyakan apa saja...',
        welcome: 'Halo! Ada yang bisa saya bantu hari ini?',
        quickReplies: ['Layanan', 'Konsultasi', 'Kontak'],
        buttonText: '💬 Obrolan',
        widgetTitle: 'Obrolan dengan kami',
        status: 'coming-soon'
    },
    ms: {
        code: 'ms',
        name: 'Bahasa Melayu',
        locale: 'ms',
        dir: 'ltr',
        placeholder: 'Tanya apa-apa...',
        welcome: 'Hai! Apa yang boleh saya bantu hari ini?',
        quickReplies: ['Perkhidmatan', 'Perundingan', 'Hubungi'],
        buttonText: '💬 Sembang',
        widgetTitle: 'Sembang dengan kami',
        status: 'coming-soon'
    },
    zh: {
        code: 'zh',
        name: '中文',
        locale: 'zh',
        dir: 'ltr',
        placeholder: '问任何问题...',
        welcome: '欢迎！今天我能为您提供什么帮助？',
        quickReplies: ['服务', '咨询', '联系我们'],
        buttonText: '💬 聊天',
        widgetTitle: '与我们聊天',
        status: 'coming-soon'
    }
};

// Fonction pour obtenir les langues actives
function getActiveChatbotLanguages() {
    return Object.keys(CHATBOT_LANGUAGES).filter(
        lang => !CHATBOT_LANGUAGES[lang].status || CHATBOT_LANGUAGES[lang].status !== 'coming-soon'
    );
}

// Fonction pour obtenir la configuration d'une langue
function getChatbotLanguage(langCode) {
    if (CHATBOT_LANGUAGES[langCode]) {
        return CHATBOT_LANGUAGES[langCode];
    }
    // Retourner l'anglais par défaut
    return CHATBOT_LANGUAGES.en;
}