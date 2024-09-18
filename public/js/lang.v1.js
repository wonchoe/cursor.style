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

            if (currentDomain !== preferredDomain) {
                const newUrl = window.location.protocol + '//' + preferredDomain + window.location.pathname + window.location.search;
                console.log('Redirecting to:', newUrl);
                window.location.href = newUrl;
            }
        }

        checkAndRedirect();
