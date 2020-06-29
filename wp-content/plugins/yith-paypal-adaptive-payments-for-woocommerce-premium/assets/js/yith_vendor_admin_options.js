(function($){
    var trigger_lock = false;
        paypal_service = $('#payment_gateway'),
        payment_method = $('#payment_method'),

        paypal_deps = function(){

        if( paypal_service.val() == 'adaptive' ){
            payment_method.val( 'manual').trigger('change');

            $(document).find('.paypal_text').show();
        }
        else{
            $(document).find('.paypal_text').hide();
            }
        };


    paypal_deps();
    
    paypal_service.on( 'change', paypal_deps );

    paypal_service.yith_wpv_option_deps( '#payment_method', 'select', 'adaptive', true );
    paypal_service.yith_wpv_option_deps( '#paypal_sandbox', 'select', 'adaptive', true );
    paypal_service.yith_wpv_option_deps( '#paypal_api_username', 'select', 'adaptive', true );
    paypal_service.yith_wpv_option_deps( '#paypal_api_password', 'select', 'adaptive', true );
    paypal_service.yith_wpv_option_deps( '#paypal_api_signature', 'select', 'adaptive', true );
    paypal_service.yith_wpv_option_deps( '#paypal_payment_mail_subject', 'select', 'adaptive', true );
    paypal_service.yith_wpv_option_deps( '#paypal_ipn_notification_url', 'select', 'adaptive', true );

    $(document).on( 'yith_wcmv_enable_opt', function(){
        if( trigger_lock === false ){
            trigger_lock = true;
            $('#payment_gateway').trigger('change');
            trigger_lock = false;
        }
    });

})(jQuery);