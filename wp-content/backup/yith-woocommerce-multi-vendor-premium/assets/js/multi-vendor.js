/*
 * Multi Vendors Frontend Script Vers 1.0
 */

(function ($) {

    var header_wrap = $('.store-header-wrapper');

    if( header_wrap.hasClass( 'small-box' ) ){
        var header_img = header_wrap.find('.store-image'),
            window_width = $(window).width();

        if (window_width >= 768 && window_width <= 979) {
            header_img.imagesLoaded(function ($) {
                    var header_height = header_wrap.height();
                    header_wrap.find('.store-image').css({
                        height  : header_height,
                        maxWidth: 'none'
                    });

                }
            );
        }

        else {
            header_img.imagesLoaded(function ($) {
                    var header_height = header_img.height();
                    header_wrap.find('.store-info').css('height', header_height);
                }
            );
        }
    }

    // Registration form
    var registration_form   = $('#yith-vendor-registration'),
        registration_check  = $('#vendor-register'),
        vendor_vat_field    = $('#vendor-vat'),
        vendor_terms        = $('#vendor-terms'),
        reg_email           = $('#customer_login #reg_email'),
        vendor_email        = $('#customer_login #vendor-email');

    registration_check.on('click', function () {
        registration_form.fadeToggle("slow", "linear");
        if( field_check.is_vat_require == true ){
            if( registration_check.is(':checked') ){
                vendor_vat_field.prop( 'required', true );
            }

            else {
                vendor_vat_field.prop( 'required', false );
            }
        }

        if( registration_check.is(':checked') ){
            vendor_terms.prop( 'required', true );
        }

        else {
            vendor_terms.prop( 'required', false );
        }
    });

    reg_email.on( 'input propertychange', function(){
        vendor_email.val( reg_email.val() );
    } );

    //Become a vendor
    var become_vendor_form = $( '#yith-become-a-vendor').find( 'form.register' );
    become_vendor_form.find('.input-text').on( 'blur', function(){
        var t = $(this);

        if (t.hasClass('yith-required')) {
            if (t.val() == '') {
                t.addClass('woocommerce-invalid').removeClass('woocommerce-validated');
            }
            else {
                t.removeClass('woocommerce-invalid').addClass('woocommerce-validated');
            }
        }
    });

    $('#yith-become-a-vendor-submit').on('click', function(event){
        var error = false;
        become_vendor_form.find('.input-text.yith-required').each(function(){
            var t       = $(this);

            if( t.val() == '' ){
                t.addClass('woocommerce-invalid').removeClass('woocommerce-validated');
                error = true;
            }
        });
        if (error == false) {
            become_vendor_form.submit();
        }
    });
})(jQuery);
