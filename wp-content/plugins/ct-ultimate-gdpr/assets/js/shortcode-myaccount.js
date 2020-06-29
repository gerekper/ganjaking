jQuery(document).ready(function ($) {

    if ( $("#tabs").tabs ) {
        $("#tabs").tabs({
            active: 0
        });
    }

    // SIMULATE CHECKBOX FUNCTION
    $( '.ct-ultimate-gdpr-container label[for*="ct-ultimate-gdpr-consent-"]' ).on( 'click', function() {
        var realCheckbox = $( this ).find( 'input[type="checkbox"]' );
        var ctCheckbox = $( this ).find( '.ct-checkbox' );
        checkboxFn( realCheckbox, ctCheckbox );
    } );
    $( '.ct-ultimate-gdpr-container .ct-ultimate-gdpr-service-options' ).on( 'click', function() {
        var realCheckbox = $( this ).find( 'input[type="checkbox"]' );
        var ctCheckbox = $( this ).find( '.ct-checkbox' );
        checkboxFn( realCheckbox, ctCheckbox );
    } );
    function checkboxFn( realCheckbox, ctCheckbox ) {
        if ( realCheckbox.is( ':checked' ) ) {
            realCheckbox.prop( 'checked', false );
            ctCheckbox.removeClass( 'ct-checked' );
        } else {
            realCheckbox.prop( 'checked', true );
            ctCheckbox.addClass( 'ct-checked' );
        }
    }

    // GO TO APPROPRIATE TAB BASED ON URL SLUG
    if ( $( '#ct-ultimate-gdpr-data-access' ).length ) {
        var url = $( location ).attr( 'href' );
        var hash = url.substring( url.indexOf( "#" ) + 1 );
        if ( hash == 'tabs-2' ) {
            setTimeout( function() {
                $( '#ui-id-2' ).trigger( 'click' )
            }, 0 );
        } else if ( hash == 'tabs-3' ) {
            setTimeout( function() {
                $( '#ui-id-3' ).trigger( 'click' )
            }, 0 );
        } else if ( hash == 'tabs-4' ) {
            setTimeout( function() {
                $( '#ui-id-4' ).trigger( 'click' )
            }, 0 );
        }
    }

    jQuery(document).on('.ct-ultimate-gdpr-my-account submit', '.ct-ultimate-gdpr-my-account form', function(e){
        e.preventDefault();
        var url = ct_ultimate_gdpr_myaccount.ajaxurl;
        var form = jQuery(this);
        var action = form.attr('id').split('-').join('_');
        var data = form.serialize();
        data += "&action=" + action + '&' + form.attr('id') + '-submit=Submit';

        form.find('input[type=submit]').attr('disabled', true);
        form.find('input[type=submit]').after('<i class="fa fa-spinner"></i>');
        form.addClass('ct-ultimate-on-process');

        jQuery.post(url, data,
            function (res) {
                if(res.notices){
                    jQuery('<div class="notice-info notice">' + res.notices + '</div>').insertBefore('#tabs');
                    form.find('input:text, input[type=email], select, textarea').val('');
                    form.find('input:radio, input:checkbox').prop('checked', false);
                    form.find('.ct-checkbox').removeClass('ct-checked');
                }else{
                    jQuery('<div class="notice-info notice">' + ct_ultimate_gdpr_myaccount.error_message + '</div>').insertBefore('#tabs');
                }
                $("html, body").animate({ scrollTop: $(".notice-info").offset().top }, "fast");
                setTimeout(function(){
                    jQuery('.notice-info').remove();

                }, 5000);
                form.find('input[type=submit]').removeAttr('disabled');
                form.find('i.fa.fa-spinner').remove();
                form.removeClass('ct-ultimate-on-process');
            }
        )
    });

});
