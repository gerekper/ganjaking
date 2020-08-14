jQuery(document).ready( function( $ ){

    /**
     * Get enhanced select labels
     *
     * @use yith_wcaf
     * @return mixed
     */
    function getEnhancedSelectFormatString() {
        return {
            formatMatches: function( matches ) {
                if ( 1 === matches ) {
                    return yith_wcaf.labels.select2_i18n_matches_1;
                }

                return yith_wcaf.labels.select2_i18n_matches_n.replace( '%qty%', matches );
            },
            formatNoMatches: function() {
                return yith_wcaf.labels.select2_i18n_no_matches;
            },
            formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
                return yith_wcaf.labels.select2_i18n_ajax_error;
            },
            formatInputTooShort: function( input, min ) {
                var number = min - input.length;

                if ( 1 === number ) {
                    return yith_wcaf.labels.select2_i18n_input_too_short_1;
                }

                return yith_wcaf.labels.select2_i18n_input_too_short_n.replace( '%qty%', number );
            },
            formatInputTooLong: function( input, max ) {
                var number = input.length - max;

                if ( 1 === number ) {
                    return yith_wcaf.labels.select2_i18n_input_too_long_1;
                }

                return yith_wcaf.labels.select2_i18n_input_too_long_n.replace( '%qty%', number );
            },
            formatSelectionTooBig: function( limit ) {
                if ( 1 === limit ) {
                    return yith_wcaf.labels.select2_i18n_selection_too_long_1;
                }

                return yith_wcaf.labels.select2_i18n_selection_too_long_n.replace( '%qty%', limit );
            },
            formatLoadMore: function( pageNumber ) {
                return yith_wcaf.labels.select2_i18n_load_more;
            },
            formatSearching: function() {
                return yith_wcaf.labels.select2_i18n_searching;
            }
        };
    }

    /**
     * Add tooltip to an UI element
     *
     * @param object
     * @param position
     */
    function addTooltip( object, position ) {

        var tooltip = object.data('tip'),
            position = position ? position : 'bottom',
            tooltip_wrapper = $('<span class="yith_wcaf_tooltip"></span>');

        if( typeof tooltip == 'undefined' || ! tooltip || object.find( '.yith_wcaf_tooltip' ).length ) {
            return;
        }

        tooltip_wrapper.addClass( position );
        object.append( tooltip_wrapper.html( '<span>' + tooltip + '</span>' ) );
    }

    /**
     * Returns true if currently on iOs device
     *
     * @return bool
     */
    function isOS() {
        return navigator.userAgent.match(/ipad|iphone/i);
    }

    /*
     * Extends jQuery object to implement commissions dashboard view js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_dashboard_commissions = function(){
        var t = $(this);

        // Ajax product search box
        t.find( ':input.wc-product-search' ).filter( ':not(.enhanced)' ).each( function() {
            var select2_args = {
                allowClear:  !! $( this ).data( 'allow_clear' ),
                placeholder: $( this ).data( 'placeholder' ),
                minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         yith_wcaf.ajax_url,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term:     term,
                            action:   'yith_wcaf_json_search_products_and_variations',
                            security: yith_wcaf.search_products_nonce
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                            $.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };

            select2_args.multiple = false;
            select2_args.initSelection = function( element, callback ) {
                var data = {id: element.val(), text: element.attr( 'data-selected' )};
                return callback( data );
            };

            select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

            $( this ).select2( select2_args ).addClass( 'enhanced' );
        });

        // datepicker
        t.find( '.datepicker').datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            beforeShow: function(input, inst) {
                $('#ui-datepicker-div')
                    .removeClass(function() {
                        return $('input').get(0).id;
                    })
                    .addClass( 'yith-wcaf-datepicker' );
            }
        });
    };

    /*
     * Extends jQuery object to implement clicks dashboard view js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_dashboard_clicks = function(){
        var t = $(this);

        // datepicker
        t.find( '.datepicker').datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            beforeShow: function(input, inst) {
                $('#ui-datepicker-div')
                    .removeClass(function() {
                        return $('input').get(0).id;
                    })
                    .addClass( 'yith-wcaf-datepicker' );
            }
        });
    };

    /*
     * Extends jQuery object to implement payments dashboard view js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_dashboard_payments = function(){
        var t = $(this);

        // datepicker
        t.find( '.datepicker').datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            beforeShow: function(input, inst) {
                $('#ui-datepicker-div')
                    .removeClass(function() {
                        return $('input').get(0).id;
                    })
                    .addClass( 'yith-wcaf-datepicker' );
            }
        });
    };

    /*
     * Extends jQuery object to implement withdraw dashboard view js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_dashboard_withdraw = function(){
        var t = $(this),
            from = t.find( '#withdraw_from' ),
            to = t.find( '#withdraw_to' ),
            country = t.find( '#billing_country' ),
            type = t.find('[name="type"]'),
            fields = t.find('input'),
            form = t.find('form'),
            validateField = function( t ){
                var name = t.attr('name'),
                    val = t.val(),
                    p = t.closest( 'p' ),
                    required = p.hasClass( 'validate-required' );

                if( required && ! val ){
                    p.addClass('yith-field-required');
                    return;
                }

                if( ( name === 'withdraw_from' || name === 'withdraw_to' ) && ! val.match( /[0-9]{4}-[0-9]{2}-[0-9]{2}/ ) ){
                    p.addClass('yith-field-required');
                    return;
                }

                if( name === 'payment_email' && ! val.match( /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/ ) ){
                    p.addClass('yith-field-required');
                    return;
                }

                p.removeClass('yith-field-required');
            };

        from.add( to ).on( 'change', function(){

            var from_val = from.val(),
                to_val = to.val(),
                data = {
                    action: 'get_withdraw_amount',
                    withdraw_from: from_val,
                    withdraw_to: to_val,
                    security: yith_wcaf.get_withdraw_amount
                };

            if( ! from_val || ! to_val ){
                return;
            }

            $.ajax( {
                type:		'POST',
                url:		yith_wcaf.ajax_url,
                data:		data,
                beforeSend: function(){
                    t.find( '.information-panel' ).block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                complete: function(){
                    t.find( '.information-panel' ).unblock();
                },
                success:	function( amount ) {
                    t.find('.withdraw-current-total').html( amount );
                },
                dataType: 'html'
            } );
        } );

        // datepicker
        t.find( '.datepicker')
            .datepicker({
                dateFormat: "yy-mm-dd",
                numberOfMonths: 1,
                showButtonPanel: true,
                beforeShow: function(input, inst) {
                    $('#ui-datepicker-div')
                        .removeClass(function() {
                            return $('input').get(0).id;
                        })
                        .addClass( 'yith-wcaf-datepicker' );
                }
            });

        // type handling
        type.on( 'change', function(){
            var checked = type.filter( ':checked' ).val(),
                firstName= $('#first_name'),
                lastName= $('#last_name'),
                company = $('#company'),
                vat = $('#vat'),
                cif= $('#cif');

            if( checked === 'business' ){
                firstName.closest( 'p' ).hide();
                lastName.closest( 'p' ).hide();
                cif.closest( 'p' ).hide();
                company.closest( 'p' ).show();
                vat.closest( 'p' ).show();
            }
            else{
                firstName.closest( 'p' ).show();
                lastName.closest( 'p' ).show();
                cif.closest( 'p' ).show();
                company.closest( 'p' ).hide();
                vat.closest( 'p' ).hide();
            }
        } ).change();

        // trigger change to billing country, to fix state
        country.trigger( 'change' );

        // validation
        fields.on( 'blur change', function(){
            validateField( $(this) );
        } );

        form.on( 'submit', function(){
            fields.filter(':visible').blur();

            if( fields.closest( 'p.form-row' ).filter('.yith-field-required').length ){
                return false;
            }
        } );
    };

    /*
     * Extends jQuery object to implement coupons dashboard view js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_dashboard_coupons = function(){
        var t = $(this);

        addTooltip( t.find( '.help_tip' ) );
    };

    /*
     * Extends jQuery object to implement generate link view js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_generate_link = function(){
        var t = $(this);

        t.find('.copy-trigger').on( 'click', function ( event ) {
            var obj_to_copy = $(this).parents('p').find( '.copy-target' );

            if ( obj_to_copy.length > 0 ) {

                if( obj_to_copy.is('input') ) {

                    if (isOS()) {

                        obj_to_copy[0].setSelectionRange(0, 9999);
                    } else {
                        obj_to_copy.select();
                    }
                    document.execCommand("copy");
                }
                else{

                    var hidden = $('<input/>', {
                        val : obj_to_copy.text(),
                        type: 'text'
                    });

                    $('body').append( hidden );

                    if (isOS()) {
                        hidden[0].setSelectionRange(0, 9999);
                    }else {
                        hidden.select();
                    }
                    document.execCommand("copy");

                    hidden.remove();

                }

                if ( ! $(document).triggerHandler('yith_wcaf_hide_link_copied_alert') ) {
                    alert(yith_wcaf.labels.link_copied_message);
                }
            }
        } );
    };

    /*
     * Extends jQuery object to implement registration form js functions
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_registration_form = function(){
        var t = $(this),
            how_promote = t.find('#how_promote'),
            custom_promote = t.find('#custom_promote').closest('.form-row');

        if( how_promote.length ){
            how_promote.on( 'change', function(){
                if( 'others' === $(this).val() ){
                    custom_promote.show();
                }
                else{
                    custom_promote.hide();
                }
            } ).change();
        }
    };

    /*
     * Extends jQuery object to implement set referrer form behaviour
     *
     * @use yith_wcaf
     */
    $.fn.yith_wcaf_set_referrer = function(){
        var t = $(this);

        t.on( 'click', 'a.show-referrer-form', function(ev){
            ev.preventDefault();

            t.find('form').slideToggle();
        } );

        t.on( 'submit', 'form.referrer-form', function(ev){
            var form = $(this),
                data = {
                    action:		    'yith_wcaf_set_referrer',
                    referrer_token:	form.find( 'input[name="referrer_code"]' ).val(),
                    security:       yith_wcaf.set_referrer_nonce
                };

            ev.preventDefault();

            form.addClass( 'processing' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            $.ajax({
                type:		'POST',
                url:		yith_wcaf.ajax_url,
                data:		data,
                success:	function( code ) {
                    $( '.woocommerce-error, .woocommerce-message' ).remove();
                    form.removeClass( 'processing' ).unblock();

                    if ( code ) {
                        form
                            .before( code )
                            .find( 'input[name="referrer_code"]' ).prop( 'disabled' );
                        form.slideUp();
                    }
                    $(document).trigger('yith_wcaf_referrer_set')
                },
                dataType: 'html'
            });

            return false;
        } );
    };

    /**
     * Performs an ajax call whenever called, if current url contains referral var
     * in order to set referral cookies
     *
     * @use yith_wcaf
     */
    $.yith_wcaf_set_cookies = function(){
        if( ! yith_wcaf.set_cookie_via_ajax ){
            return;
        }

        var urlParams = new URLSearchParams( window.location.search );

        if( urlParams.has( yith_wcaf.referral_var ) ){
            $.get( yith_wcaf.ajax_url + '?action=yith_wcaf_ajax_set_cookie&' + yith_wcaf.referral_var + '=' + urlParams.get( yith_wcaf.referral_var ) );
        }
    };

    $( '.yith-wcaf-commissions').yith_wcaf_dashboard_commissions();
    $( '.yith-wcaf-clicks').yith_wcaf_dashboard_clicks();
    $( '.yith-wcaf-payments').yith_wcaf_dashboard_payments();
    $( '.yith-wcaf-withdraw').yith_wcaf_dashboard_withdraw();
    $( '.yith-wcaf-coupons').yith_wcaf_dashboard_coupons();
    $( '.yith-wcaf-set-referrer').yith_wcaf_set_referrer();
    $( '.yith-wcaf-link-generator').yith_wcaf_generate_link();
    $( '.yith-wcaf-registration-form' ).add( '.woocommerce-form-register' ).add( '.yith-wcaf-settings' ).yith_wcaf_registration_form();

    $.yith_wcaf_set_cookies();
} );