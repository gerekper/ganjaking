/**
 * age popup features
 * @var object ct_ultimate_gdpr_age - from wp_localize_script
 * @var object ct_ultimate_gdpr_age_shortcode_popup - from wp_localize_script
 *
 * */
jQuery(document).ready(function ($) {

    function hidePopup() {
        $('#ct-ultimate-gdpr-age-popup').hide();
    }

    function showPopup() {
        $('#ct-ultimate-gdpr-age-popup').show();
    }

    function getCookie(name) {
        const ctCookie = document.cookie;
        if (ctCookie) {
            const match = ctCookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            if (match) return match[2];
        } else {
            return '';
        }

    }

    function isConsentValid() {

        const cookieValue = getCookie('ct-ultimate-gdpr-age');
        const cookieObject = cookieValue ? JSON.parse(atob(decodeURIComponent(cookieValue))) : {};

        reloadPageIfFromBackButtonInBrowser(cookieObject);

        if (!cookieObject.date) {
            return false;
        }

        //checking for latest/legacy expire_time js implementation
        if (cookieObject.consent_expire_time) {
            return cookieObject.consent_expire_time > +new Date / 1000;
        } else if (cookieObject.expire_time) {
            return cookieObject.expire_time > +new Date / 1000;
        } else {
            return false;
        }

    }

    // hide popup
    if (isConsentValid()) {
        hidePopup();
        scheduledRedirect();
    } else {
        showPopup();
    }

    function setJsCookie(date) {

        try {

            const consent_expire_time = ct_ultimate_gdpr_age.consent_expire_time;
            const consent_time = ct_ultimate_gdpr_age.consent_time;
            var content = {
                'date'               : date,
                'consent_expire_time': consent_expire_time,
                'consent_time'       : consent_time
            };

            content = btoa(JSON.stringify(content));
            var js_expire_time = new Date(1000 * consent_expire_time).toUTCString();

            // Check if the site is accessed via HTTPS
            var cookieSecureFlag = '';
            if (window.location.protocol === 'https:') {
                cookieSecureFlag = '; Secure';
            }

            document.cookie = "ct-ultimate-gdpr-age=" + content + "; expires=" + js_expire_time + "; path=/" + cookieSecureFlag;

        } catch (e) {

        }

    }

    function onAccept() {

		jQuery('#ct-ultimate-gdpr-age-accept').attr('disabled', false);
		
        const dateInputValue =
                  $("[name='ct-ultimate-gdpr-age-date-of-birth-month']").val()
                  + '/'
                  + $("input[name='ct-ultimate-gdpr-age-date-of-birth-day']").val()
                  + '/'
                  + $("input[name='ct-ultimate-gdpr-age-date-of-birth-year']").val();

        const date = new Date(dateInputValue);
        setJsCookie(date);

        jQuery.post(ct_ultimate_gdpr_age.ajaxurl, {
                "action"                   : "ct_ultimate_gdpr_age_set_date",
                "ct-ultimate-gdpr-age-date": date,
            }
        );

        const age = dateToAge(date);

        if (age >= ct_ultimate_gdpr_age.age_limit_to_sell) {

            // do nothing
            // window.location.reload(true);

        } else if (age < ct_ultimate_gdpr_age.age_limit_to_enter) {

            // for ages below 13, redirect to my account for user to be able to enter guard data
            if (ct_ultimate_gdpr_age.my_account_page_url) {
                jQuery('#ct-ultimate-gdpr-age-accept').attr('disabled', true);
                setTimeout(function() { window.location.href = ct_ultimate_gdpr_age.my_account_page_url; }, 1000);
            }

            // do not hide popup
            return;

        } 
  
        // redirect to terms/privacy if active
        if (ct_ultimate_gdpr_age.scheduled_redirect) {
            
            jQuery('#ct-ultimate-gdpr-age-accept').attr('disabled', true);
            setTimeout(function() { window.location.href = ct_ultimate_gdpr_age.scheduled_redirect; }, 1000);
        }

        hidePopup()
        $(document).trigger('ct-age-clicked');

    }
 
	$('#ct-ultimate-gdpr-age-accept').bind('click', onAccept);
	$('body').on('click touchstart', '#ct-ultimate-gdpr-age-accept', onAccept);
	
	
    $(window).on('load', function () {
        
        const consentCookieValue = getCookie('ct-ultimate-gdpr-age');

        if (consentCookieValue) {
            hidePopup();
        }

    });

    function dateToAge(date) {
        const msDiff = Date.now() - date.getTime();
        const diffDate = new Date(msDiff);

        return Math.abs(diffDate.getUTCFullYear() - 1970);
    }

    function scheduledRedirect() {

        // always redirect if scheduled_redirect is not empty
        if (ct_ultimate_gdpr_age.scheduled_redirect && ct_ultimate_gdpr_age.scheduled_redirect !== ct_ultimate_gdpr_age.my_account_page_url) {
            setTimeout(function() { window.location.href = ct_ultimate_gdpr_age.scheduled_redirect; }, 1000);
        }
    }

});

/**
 * We implement both available api for checking back button in browser for compatibility,
 * because one is deprecated and second is in state of recomendation and not yet fully implemented everywhere
 * @param cookieObject
 */
function reloadPageIfFromBackButtonInBrowser (cookieObject) {

    if (cookieObject && window.performance) {
        var navigationEntries = PerformanceEntry.entryType;
        if (navigationEntries === 'back_forward') {
            window.location.reload(true)
        } 
      
    }

    if (cookieObject && window.performance && window.performance.navigation && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        window.location.reload(true)
    }
}