/** Frontend consent checkbox validation */

jQuery(function () {

    // get all consent checkboxes
    var consentFields = jQuery('.ct-ultimate-gdpr-consent-field');

    // disable submit button if consent not checked
    function toggleSubmit(field) {
        field.closest('form').find('input[type = submit]').attr('disabled', !field.attr('checked'));
    }

    // toggle all at the beginning
    consentFields.each(function () {
        toggleSubmit(jQuery(this));
    });

    // toggle on checkbox change
    consentFields.change(function () {
        toggleSubmit(jQuery(this));
    })

    jQuery('#whats-new').on( 'focus blur', function() {

        // toggle all
        consentFields.each(function () {
            toggleSubmit(jQuery(this));
        });

    });

});