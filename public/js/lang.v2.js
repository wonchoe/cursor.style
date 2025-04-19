(function () {
    const cookieName = "lang_redirect_disabled";

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    function setCookie(name, value, seconds) {
        const expires = new Date(Date.now() + seconds * 1000).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/';
    }

    const lang = navigator.language || navigator.userLanguage;
    const shortLang = lang.slice(0, 2).toLowerCase();
    const host = window.location.hostname;

    const supportedLangs = [
        "am", "ar", "bg", "bn", "ca", "cs", "da", "de", "el", "en", "es", "et", "fa", "fi", "fil",
        "fr", "gu", "he", "hi", "hr", "hu", "id", "it", "ja", "kn", "ko", "lt", "lv", "ml", "mr",
        "ms", "nl", "no", "pl", "pt", "ro", "ru", "sk", "sl", "sr", "sv", "sw", "ta", "te", "th", "tr",
        "uk", "vi", "zh"
    ];

    if (host === 'cursor.style' && !getCookie(cookieName)) {
        if (supportedLangs.includes(shortLang)) {
            setCookie(cookieName, '1', 60);
            const targetHost = `${shortLang}.cursor.style`;
            window.location.href = `https://${targetHost}${window.location.pathname}`;
        }
    }
})();
