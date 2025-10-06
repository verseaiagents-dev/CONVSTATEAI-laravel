// Widget Configuration - Environment-based
// Bu dosya build time'da environment değişkenleri ile güncellenir

// Environment değişkenlerini al (build time'da inject edilir)
const getEnvVar = (name, defaultValue) => {
    // Build time'da bu değerler gerçek environment değerleri ile değiştirilir
    return window.ENV_VARS && window.ENV_VARS[name] !== undefined 
        ? window.ENV_VARS[name] 
        : defaultValue;
};

window.WIDGET_CONFIG = {
    // API Endpoints - Environment-based
    API_BASE_URL: getEnvVar('REACT_APP_API_BASE_URL', 'https://convstateai.com'),
    CHAT_ENDPOINT: '/api/chat',
    PRODUCTS_ENDPOINT: '/api/products',
    WIDGET_CUSTOMIZATION_ENDPOINT: '/api/widget-customization',
    
    // Default AI Settings - Environment-based
    DEFAULT_AI_NAME: getEnvVar('REACT_APP_DEFAULT_AI_NAME', 'ConvState AI'),
    DEFAULT_WELCOME_MESSAGE: getEnvVar('REACT_APP_DEFAULT_WELCOME_MESSAGE', 'Merhaba ben Kadir, senin dijital asistanınım. Sana nasıl yardımcı olabilirim?'),
    
    // Session Management
    SESSION_STORAGE_KEY: 'chat_session_id',
    
    // Product Display Settings
    MAX_PRODUCTS_TO_SHOW: 6,
    PRODUCT_IMAGE_PATH: '/imgs/',
    
    // Chat Settings
    TYPING_DELAY: 1000,
    MAX_MESSAGE_LENGTH: 1000,
    
    // Feature Flags - Environment-based
    ENABLE_TTS: getEnvVar('REACT_APP_ENABLE_TTS', 'true') === 'true',
    ENABLE_PRODUCT_RECOMMENDATIONS: getEnvVar('REACT_APP_ENABLE_PRODUCT_RECOMMENDATIONS', 'true') === 'true',
    ENABLE_CAMPAIGN_TAB: getEnvVar('REACT_APP_ENABLE_CAMPAIGN_TAB', 'true') === 'true',
    ENABLE_FAQ_TAB: getEnvVar('REACT_APP_ENABLE_FAQ_TAB', 'true') === 'true'
};

// Helper function to get config values
window.getWidgetConfig = function(key) {
    return window.WIDGET_CONFIG[key] || null;
};

// Helper function to build API URL
window.buildApiUrl = function(endpoint) {
    return window.WIDGET_CONFIG.API_BASE_URL + endpoint;
};
