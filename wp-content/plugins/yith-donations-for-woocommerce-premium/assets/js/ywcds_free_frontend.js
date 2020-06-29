/**
 * Created by Your Inspiration on 30/06/2015.
 */

jQuery(document).ready( function( $ ) {

    var add_donation_to_cart    =   function ( el, form ){

            var add_to_cart_info = form.serializeFormJSON();

            add_to_cart_info.add_donation_to_cart = -1 ;
            add_to_cart_info.action = yith_wcds_frontend_l10n.actions.add_donation_to_cart ;


        var current_form    =  el.parents('.ywcds_form_container');
        $.ajax({
            type: 'GET',
            url: yith_wcds_frontend_l10n.ajax_url,
            data: add_to_cart_info,
            dataType: 'json',
            beforeSend: function(){
               current_form.find( '.ajax-loading' ).css( 'visibility', 'visible' );
            },
            complete: function(){
                current_form.find( '.ajax-loading' ).css( 'visibility', 'hidden' );
            },
            success: function( response ) {


                var this_page = window.location.toString();

                this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
                var message_container   =   current_form.find('.ywcds_message');

                if( response.fragments ) {
                    update_cart(el, this_page, response);

                    message_container.removeClass('woocommerce-error');

                    if( typeof ywcds_params!=='undefined' && typeof ywcds_params.donation_in_cart !== 'undefined' )
                        ywcds_params.donation_in_cart   =   1;

                    var view_cart   =   '';
                    // View cart text
                    if ( ! wc_add_to_cart_params.is_cart && !el.parent().find( '.added_to_cart' ).length ) {
                        view_cart= ' <a href="' + wc_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' +
                        wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>'  ;
                    }
                    message_container.html( yith_wcds_frontend_l10n.messages.success+ view_cart );

                    if( yith_wcds_frontend_l10n.redirect_after_add_to_cart){

                        window.location.replace( yith_wcds_frontend_l10n.redirect_after_add_to_cart );
                    }

                }
                else {

                    var response_result = response.result,
                        response_message = response.message;

                        current_form.find('.ywcds_amount').css('border', '1px solid red');
                        message_container.addClass('woocommerce-error');
                        message_container.html(response_message);
                }

                message_container.show();

            }
        });

    },
        update_cart             =   function( el, this_page, response){

        fragments   =   response.fragments;
        cart_hash   =   response.cart_has;
        // Block fragments class
        if ( fragments ) {
            $.each( fragments, function( key, value ) {
                $( key ).addClass( 'updating' );
            });
        }

        // Block widgets and fragments
        $( '.shop_table.cart, .updating, .cart_totals' ).fadeTo( '400', '0.6' ).block({
            message: null,
            overlayCSS: {
                opacity: 0.6
            }
        });

        // Changes button classes
        el.addClass( 'added' );



        // Replace fragments
        if ( fragments ) {
            $.each( fragments, function( key, value ) {
                $( key ).replaceWith( value );
            });
        }

        // Unblock
        $( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();

        // Cart page elements
        $( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {

            $( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();

            $( 'body' ).trigger( 'cart_page_refreshed' );
        });

        $( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
            $( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
        });
    };

    $(document).on('click', '.ywcds_submit_widget.ywcds_ajax_add_donation', function( ev ){


        var t           =       $(this),
            form        =       t.parents('#ywcds_add_donation_form'),
            amount      =       form.find('.ywcds_amount').val();

            ev.preventDefault();

        add_donation_to_cart( t,form );
        return false;

    });

    $(document).on('click', '.single_add_to_cart_button', function( el ){

        var t                       =   $(this),
            form                    =   $(document).find('#ywcds_add_donation_form_single_product'),
            amount_single_product   =   form.find('.ywcds_amount_single_product'),

            is_obligatory           =   form.data( 'donation_is_obligatory'),
            min                     =   form.data('min_donation'),
            max                     =   form.data( 'max_donation'),
            woocommerce_breadcrumb  =   $(document).find('.woocommerce-breadcrumb'),
            error_message           =   '<div class="woocommerce-error">';

      if( form.length ){

        $('.woocommerce-error, .woocommerce-message').remove();

        var check   =   check_amount( amount_single_product.val(),min, max, is_obligatory );

        if( check!= 'ok' ) {

            switch (check) {

                case 'nan' :
                    error_message += yith_wcds_frontend_l10n.messages.no_number + '</div>';
                    break;
                case 'less_zero' :
                    error_message += yith_wcds_frontend_l10n.messages.negative + '</div>';
                    break;
                case 'empty':
                    error_message += yith_wcds_frontend_l10n.messages.obligatory + '</div>';
                    break;
                case 'min'  :
                    error_message += yith_wcds_frontend_l10n.messages.min_don + '</div>';
                    break;
                case 'max'  :
                    error_message += yith_wcds_frontend_l10n.messages.max_don + '</div>';
                    break;

            }

            woocommerce_breadcrumb.after(error_message);
            $(document).scrollTop(0);
            el.preventDefault();
            $(document).trigger('ywcds_error_value',[$(this)] );
            return false;
        }
        }

    });

    var check_amount    =   function( value, min, max, is_obligatory ){

       var  regex    = new RegExp( '[^\-0-9\%\\' + yith_wcds_frontend_l10n.mon_decimal_point + ']+', 'gi' ),
            newprice = value.replace( regex, '');

        if( value!== newprice  )
            return 'nan';

        if( value=='' && is_obligatory )
            return 'empty';


        newprice = newprice.replace( yith_wcds_frontend_l10n.mon_decimal_point, '.' );
        value = parseFloat( newprice );

        if( value<0 )
            return 'less_zero';


        if( value!='' ) {

            if( min!='' ) {
                min = ''+min+''.replace(yith_wcds_frontend_l10n.mon_decimal_point, '.');
                min = parseFloat(min);
                if( value<min )
                    return 'min';
            }

            if( max!=''){
                max = ''+max+''.replace(yith_wcds_frontend_l10n.mon_decimal_point, '.');
                max = parseFloat(max);

                if( value>max )
                return 'max';
            }
        }

        return 'ok'
    }


    $(document).on('click', '.ywcdp_single_amount', function(e){


       if( $(this).is('input:radio')){
           var amount = $(this).val();
       }else {
           var hidden_input = $(this).find('input'),
               amount = hidden_input.val();
       }

       $(this).parents('#ywcds_add_donation_form').find('.ywcds_amount').val( amount );


    });

    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
});