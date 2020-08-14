jQuery(document).ready( function($){
    "use strict";

    var $body = $('body');


    $( document ).on( 'click' , '.ywpar-button-message', function( e ) {

        e.preventDefault();
        var $t = $(this);
        if( $t.hasClass('ywpar-button-percentage-discount') ){
            $t.next().find('form').submit();
        }else{
            $('.ywpar_apply_discounts_container').slideToggle();
        }

    });


    $( document ).on( 'click' , '#ywpar_apply_discounts', function( e ) {
        e.preventDefault();
        var min_val = $('#ywpar-points-max').attr('min');

        if ( parseFloat( $('#ywpar-points-max').val() ) < parseFloat( min_val ) ) {
            $('.ywpar_min_reedem_value_error').show();
           return false;
        } else {
            $('.ywpar_min_reedem_value_error').css('opacity','0');
            $('#ywpar_input_points_check').val(1);
            $(this).parents('form').submit();
        }

    });

    $.fn.yith_ywpar_variations = function( $form ) {
        var $form = $('.variations_form:not(.in_loop )'),
            $point_message = $form.closest('.product').find('.yith-par-message'),
            $point_message_variation = $form.closest('.product').find('.yith-par-message-variation');

        if( ! $point_message.length ){
            $point_message = $('.product').find('.yith-par-message');
			$point_message_variation = $('.product').find('.yith-par-message-variation');
        }

        if( ! $point_message.length ){
            $point_message = $('.yith-par-message');
			$point_message_variation = $('.yith-par-message-variation');
        }

        var $points = $point_message_variation.find('.product_point'),
            $points_conversion = $point_message_variation.find('.product-point-conversion'); //variation_price_discount_fixed_conversion

        $form.on( 'found_variation', function( event, variation ){
			$point_message.addClass('hide');
			$point_message_variation.removeClass('hide');

            if( variation.variation_points == 0 ){
				$point_message_variation.addClass('hide');
            }if( variation.variation_points  ){
                $points.text(variation.variation_points);
            }

            if( variation.variation_price_discount_fixed_conversion ){
                $points_conversion.html(variation.variation_price_discount_fixed_conversion);
            }
        });

        $form.on( 'reset_data', function(){
			$point_message_variation.addClass('hide');
			$point_message.removeClass('hide');
        });

        $form.find('select').first().trigger('change');
    };

    if( $body.hasClass('single-product') ){
        $.fn.yith_ywpar_variations();
    }

    $(document).on( 'qv_loader_stop', function(){
        //if( $body.hasClass('single-product') ){

        $.fn.yith_ywpar_variations();
        //}
    } );

    $(document.body).on('updated_cart_totals, added_to_cart, update_checkout', function () {

        // cart messages

        var $message_container = $('#yith-par-message-cart');

        if ( $message_container.length > 0 ) {

            $.ajax({
                url       : yith_wpar_general.wc_ajax_url.toString().replace('%%endpoint%%', 'ywpar_update_cart_messages'),
                type      : 'POST',
                beforeSend: function () {
                },
                success   : function (res) {
                    if( '' !== res ){
                        $message_container.show().html( res );
                    }else{
                        $message_container.hide();
                    }
                }
            });

        }

        // cart rewards messages

        var $message_reward_container = $('#yith-par-message-reward-cart');

        if( $message_reward_container.length === 0 ){

            var $coupon_form = $('.woocommerce-form-coupon');

            $coupon_form.after('<div id="yith-par-message-reward-cart" class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info"></div>');
        }

        if ( $(document).find('#yith-par-message-reward-cart').length > 0 ) {

            $.ajax({
                url       : yith_wpar_general.wc_ajax_url.toString().replace('%%endpoint%%', 'ywpar_update_cart_rewards_messages'),
                type      : 'POST',
                beforeSend: function () {
                },
                success   : function (res) {
                    if( '' !== res ){
                        $(document).find('#yith-par-message-reward-cart').show().html( res );
                    }else{
                        $(document).find('#yith-par-message-reward-cart').hide();
                    }

                }
            });

        }

    });





});