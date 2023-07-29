window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}

function setConsent(consent) {
    const consentMode = {
        'functionality_storage': consent.functionality_storage ? 'granted' : 'denied',
        'security_storage': consent.security_storage ? 'granted' : 'denied',
        'ad_storage': consent.ad_storage ? 'granted' : 'denied',
        'analytics_storage': consent.analytics_storage ? 'granted' : 'denied',
        'personalization': consent.personalization ? 'granted' : 'denied',
    };

    gtag('consent', 'update', consentMode);
    localStorage.setItem('consentMode', JSON.stringify(consentMode));
}

if(localStorage.getItem('consentMode') === null){
    gtag('consent', 'default', {
        'ad_storage': 'denied',
        'analytics_storage': 'denied',
        'personalization_storage': 'denied',
        'functionality_storage': 'denied',
        'security_storage': 'denied',
    });
} else {
    gtag('consent', 'default', JSON.parse(localStorage.getItem('consentMode')));
}

<!-- Google Tag Manager -->
(function (w, d, s, l, i) {
    w[l] = w[l] || []; w[l].push({
        'gtm.start':
            new Date().getTime(), event: 'gtm.js'
    }); var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
})(window, document, 'script', 'dataLayer', ct_ultimate_gdpr_service_gtm.id );
<!-- End Google Tag Manager -->

jQuery(document).ready(function ($) {

    if(!ct_ultimate_gdpr_service_gtm.id) return false;

    $('body').find('.ct-ultimate-gdpr-cookie-modal-btn.save, #ct-ultimate-gdpr-cookie-accept').on('click',function(){
        let consent = {};
        $('body').find('li input.ct-ultimate-gdpr-cookie-modal-single-item.ct-cookie-item-selected:checked').each(function(e) {
            // 5 = essentials = personalization
            // 6 = functionality = functionality_storage
            // 7 = analytics = analytics_storage
            // 8 = advertising = ad_storage
            if ($(this).val() === '5' ) consent.personalization = ($(this).val() === '5');
            if ($(this).val() === '5' ) consent.security_storage  = ($(this).val() === '5');
            if ($(this).val() === '6' ) consent.functionality_storage  = ($(this).val() === '6');
            if ($(this).val() === '7' ) consent.analytics_storage = ($(this).val() === '7');
            if ($(this).val() === '8' )  consent.ad_storage = ($(this).val() === '8');
        });

        setConsent(consent);

    })
})



