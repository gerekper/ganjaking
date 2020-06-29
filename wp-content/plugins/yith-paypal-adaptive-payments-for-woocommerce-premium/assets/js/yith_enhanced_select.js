jQuery(document).ready(function($){


    $(document.body).on( 'ywpadp-enhanced-init', function(e){

        var ywpad_customers_search = $('.wc-customer-search.yith_receiver_user_id').filter(':not(.hidden)');

            ywpad_customers_search.each( function () {
              
            if( ywpadp_select2_param.is_wc_3 ){

                $(this).on('select2:select', function (e) {
                    ywpad_search_paypal_email(e);
                });
            }else{
                $(this).on('select2-select', function (e) {
                    ywpad_search_paypal_email(e);
                });
            }
        });
        
    } ).trigger( 'ywpadp-enhanced-init' );

    function ywpad_search_paypal_email(e){

        var target = $(e.currentTarget),
            data_ajax = {
                user_id: target.val(),
                action: 'paypal_adptive_payments_search_paypal_email',
                security: ywpadp_select2_param.search_customers_nonce
            },
            parent = target.parents('.form-field'),
            email_field = parent.find('.yith_receiver_email'),
            block_params = {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                },
                ignoreIfBlocked: true
            };


        parent.block(block_params);
        $.ajax({
            type: 'POST',
            url: ywpadp_select2_param.ajax_url,
            data: data_ajax,
            dataType: 'json',
            success: function (response) {

                setTimeout(function () {
                    parent.unblock()
                }, 500);
                email_field.val('' + response.result);
            }

        });
    };
});