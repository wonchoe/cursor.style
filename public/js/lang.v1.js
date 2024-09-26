function isBot() {
    var userAgent = navigator.userAgent.toLowerCase();
    var botPatterns = [
        'googlebot', // Googlebot
        'bingbot', // Bingbot
        'yandexbot', // YandexBot
        'duckduckbot', // DuckDuckGo
        'slurp', // Yahoo Slurp
        'baiduspider', // Baidu
        'sogou', // Sogou
        'exabot', // Exabot
        'facebot', // Facebook
        'ia_archiver', // Alexa (Amazon)
        'mj12bot', // Majestic-12
        'seznambot', // Seznam
        'gigabot', // Gigablast
        'crawler', // Загальне "crawler"
        'spider', // Загальне "spider"
        'robot', // Загальне "robot"
        'bot', // Загальне "bot"
        'python-requests' // Підхід через requests (інколи боти роблять такі запити)
    ];
    
    return botPatterns.some(function (pattern) {
        return userAgent.indexOf(pattern) !== -1;
    });
}


function getBrowserLanguage() {
    return (navigator.language || navigator.userLanguage).split('-')[0].toLowerCase();
}

const localeDomains = {
    'ru': 'ru.cursor.style',
    'es': 'es.cursor.style',
    'uk': 'ua.cursor.style'
};

function getPreferredDomain(browserLanguage) {
    return localeDomains[browserLanguage] || 'cursor.style';
}

function checkAndRedirect() {
    const browserLanguage = getBrowserLanguage();
    const currentDomain = window.location.hostname;
    const preferredDomain = getPreferredDomain(browserLanguage);

    if (preferredDomain === 'dev.cursor.style')
        return;
    if (window.location.hostname === 'localhost')
        return;
    if (window.location.hostname === '127.0.0.1')
        return;


    if (currentDomain !== preferredDomain) {
        const newUrl = window.location.protocol + '//' + preferredDomain + window.location.pathname + window.location.search;
        console.log('Redirecting to:', newUrl);
        window.location.href = newUrl;
    }
}

if (!isBot()) checkAndRedirect();
