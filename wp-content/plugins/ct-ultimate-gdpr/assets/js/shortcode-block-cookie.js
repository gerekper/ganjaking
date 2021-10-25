jQuery(document).ready(function($){
    var form = $('#ct-ultimate-gdpr-block-cookies');
    var submit = $('.ct-ultimate-gdpr-cookie-block-btn');
    var level = $('#ct-ultimate-gdpr-block-cookies .level').val();
    var checkbox = $('.ct-ultimate-gdpr-block-cookies-checkbox');
    var notificaton = $('.notification');
    var check_count = 0;

    checkbox.on('click', function(){
      check_count++;
      submit.attr('disabled', false);
      if(checkbox.is(':checked')){
        level = 1;
      } else {
        level = 5;
      }
    });

   

    function Save(e) {
        
        e.preventDefault();

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

        if(check_count > 0) {
          notificaton.after('<p class="result-notification">Successfully submitted!</p>');
        }

        if(!ct_ultimate_gdpr_cookie.reload){
          setTimeout(function(){
            $('.result-notification').remove().fadeIn('slow');
          },2000);
        }
  

    }


    submit.bind('click', Save);


});