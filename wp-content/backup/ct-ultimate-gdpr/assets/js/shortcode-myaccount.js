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
        var userAgeLimit = parseInt(ct_ultimate_gdpr_myaccount.user_age_limit, 10);
        var guardAgeLimit = parseInt(ct_ultimate_gdpr_myaccount.guard_age_limit, 10);
        var ageLimitMessage = ct_ultimate_gdpr_myaccount.age_limit_message;
        var form = jQuery(this);
        var action = form.attr('id').split('-').join('_');
        var data = form.serialize();
        data += "&action=" + action + '&' + form.attr('id') + '-submit=Submit';

        form.find('input[type=submit]').attr('disabled', true);
        form.find('input[type=submit]').after('<i class="fa fa-spinner"></i>');
        form.addClass('ct-ultimate-on-process');

        var getAge = function(dateString) {
            var today = new Date();
            var birthDate = new Date(dateString);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();

            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age;
        }

        var userAge = getAge(form.find('#ct-ultimate-gdpr-age-date').val());
        var guardAge = getAge(form.find('#ct-ultimate-gdpr-age-guard-date').val());

        jQuery.post(url, data,
            function(res) {
                if (res.notices) {
                    if (userAge >= userAgeLimit || guardAge >= guardAgeLimit) {
                        jQuery('<div class="notice-info notice">' + res.notices + '</div>').insertBefore('#tabs');
                        form.find('input:text, input[type=email], select, textarea').not('[name=ct-ultimate-gdpr-age-guard-name]').val('');
                        form.find('input:radio, input:checkbox').prop('checked', false);
                        form.find('.ct-checkbox').removeClass('ct-checked');
                    } else {
                        jQuery('<div class="notice-info has-error">' + ageLimitMessage + '</div>').insertBefore('#tabs');
                    }
                } else {
                    jQuery('<div class="notice-info notice">' + ct_ultimate_gdpr_myaccount.error_message + '</div>').insertBefore('#tabs');
                }

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
