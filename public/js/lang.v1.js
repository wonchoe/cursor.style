function isBot() {
    var userAgent = navigator.userAgent.toLowerCase();
    var botPatterns = [
        'googlebot', 'bingbot', 'yandexbot', 'duckduckbot',
        'slurp', 'baiduspider', 'sogou', 'exabot', 'facebot',
        'ia_archiver', 'mj12bot', 'seznambot', 'gigabot',
        'crawler', 'spider', 'robot', 'bot',
        'python-requests', 'pagespeedinsights', 'googleweblight', 'google'
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
    return localeDomains[browserLanguage] || null;
}

function checkAndRedirect() {
    const browserLanguage = getBrowserLanguage();
    const currentDomain = window.location.hostname;
    const preferredDomain = getPreferredDomain(browserLanguage);

    if (!preferredDomain) return; // не підтримується — не редиректимо
    if (currentDomain === preferredDomain) return;

    const newUrl = window.location.protocol + '//' + preferredDomain + window.location.pathname + window.location.search;
    console.log('Redirecting to:', newUrl);
    window.location.href = newUrl;
}

if (!isBot()) checkAndRedirect();
