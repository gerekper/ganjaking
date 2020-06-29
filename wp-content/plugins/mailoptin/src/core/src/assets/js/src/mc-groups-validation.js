define(["jquery"], function ($) {
    $(document.body).on('mo_validate_optin_form_fields', function (e, mailoptin_optin, $optin_css_id, optin_js_config) {
        var choices_obj = $('#' + $optin_css_id).find('.mo-mailchimp-interest-choice');
        if (choices_obj.length === 0) return;
        if (optin_js_config.mailchimp_segment_required === false) return;
        var checked = false;
        choices_obj.each(function () {
            if (this.checked) checked = true;
        });
        if (checked === false) {
            mailoptin_optin.display_optin_error.call(undefined, $optin_css_id, optin_js_config.mailchimp_segment_required_error);
            return false;
        }
    });
});