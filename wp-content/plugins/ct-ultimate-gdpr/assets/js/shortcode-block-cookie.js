jQuery(document).ready(function($){
    
    var submit = $('.ct-ultimate-gdpr-cookie-modal-btn.save');
    var checkbox = $('.ct-ultimate-gdpr-block-cookies');

  
        checkbox.on('click', function(){
            if(checkbox.is(':checked')){
                submit.attr('disabled', false);
               
            } else {
                submit.attr('disabled', true);
                
            }
        })
  

    function onSave(e) {

        e.preventDefault();
        var level = $('#ct-ultimate-gdpr-block-cookies .level').val();
        document.cookie = 'ct-ultimate-gdpr-cookie=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';

        $.post(ct_ultimate_gdpr_cookie.ajaxurl, {
            "action": "ct_ultimate_gdpr_cookie_consent_give",
            "level": level
        }, function () {
            if (ct_ultimate_gdpr_cookie.reload) {
                window.location.reload(true);
            }
        }).fail(function () {
            $.post(ct_ultimate_gdpr_cookie.ajaxurl, {
                "skip_cookies": true,
                "action": "ct_ultimate_gdpr_cookie_consent_give",
                "level": level
            }, function () {
                setJsCookie(level);
                if (ct_ultimate_gdpr_cookie.reload) {
                    window.location.reload(true);
                }
            })

        });


    }

    submit.bind('click', onSave);


});