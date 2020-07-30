jQuery( function ( $ ) {

    var wc_checkout_coupons = {
        init : function () {
            $( document.body ).on( 'click' , '.woocommerce-remove-coupon' , this.remove_coupon ) ;
            $( 'form.checkout_coupon' ).hide().submit( this.submit ) ;
        } ,
        submit : function () {
            var $form = $( this ) ;

            var data = {
                security : wc_checkout_params.apply_coupon_nonce ,
                coupon_code : $form.find( 'input[name="coupon_code"]' ).val()
            } ;

            $.ajax( {
                type : 'POST' ,
                url : wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%' , 'apply_coupon' ) ,
                data : data ,
                success : function ( code ) {
                    $( '.woocommerce-error, .woocommerce-message' ).remove() ;
                    $form.removeClass( 'processing' ).unblock() ;

                    if ( code ) {
                        $form.before( code ) ;
                        $form.slideUp() ;

                        $( document.body ).trigger( 'update_checkout' , { update_shipping_method : false } ) ;
                        // Commented in V25.3 because on applying coupon, loading occurs causes coupon messages not shown properly.
                        // location.reload() ;
                    }
                } ,
                dataType : 'html'
            } ) ;

            return false ;
        } ,
        remove_coupon: function (e) {
            e.preventDefault();
            var $datacoupon = $(e.target).data('coupon');
            var available_msg_check = checkoutscript_variable_js.rs_available_message_check;
            var redeem_restriction = checkoutscript_variable_js.redeem_restriction;
            var checkout_redeem_check = checkoutscript_variable_js.checkout_redeem_check;
            //console.log($datacoupon);
            var data = {
                action: 'remove_sumo_coupon',
                coupon: $datacoupon,
            };
            $.post(checkoutscript_variable_js.wp_ajax_url, data, function (response) {
                if (true === response.success) {
                    if (redeem_restriction == 1 || redeem_restriction == 3 || redeem_restriction == 5) {
                        $(".woocommerce-form-coupon-toggle").show();
                        $(".coupon").parent().show();
                    }
                    $('.sumo_reward_points_manual_redeem_error_message').hide();
                    $('.sumo_reward_points_auto_redeem_error_message').remove();
                    if (available_msg_check == 'yes') {
                        $('.sumo_available_points').show();
                    }
                    if (response.data.showredeemfield) {
                        if (redeem_restriction == 1 || redeem_restriction == 2 || redeem_restriction == 5) {
                            if (checkout_redeem_check == 1) {
                                $(".checkoutredeem").show();
                                $(".rs_button_redeem_checkout").show();
                                $(".fp_apply_reward").show();
                            }
                        }
                    }
                } else {
                    window.alert(response.data.error);
                }
            } ) ;
        }
    } ;
   wc_checkout_coupons.init() ;
} ) ;


