/**
 * WooChimp Plugin JavaScript
 */

/**
 * Based on jQuery
 */
jQuery(document).ready(function() {

    /**
     * Email validation
     */
    function woochimp_validate_email(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    /**
     * Name validation
     */
    function woochimp_validate_name(name) {
        var regex = /^[\w\s\.\-\'\"]+$/;
        return regex.test(name);
    }

    /**
     * Subscribe from shortcode
     */
    jQuery('#woochimp_shortcode_subscription_submit').click(function() {
        woochimp_shortcode_subscription_submit();
    });

    jQuery('.woochimp_shortcode_content').find('input[type="text"], input[type="email"]').keydown(function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            woochimp_shortcode_subscription_submit();
        }
    });

    function woochimp_shortcode_subscription_submit() {

        // Remove previously set errors
        jQuery('#woochimp_shortcode_subscription_email').css('border-color', '');
        jQuery('#woochimp_shortcode_subscription_first_name').css('border-color', '');
        jQuery('#woochimp_shortcode_subscription_last_name').css('border-color', '');

        var form_ok = true;

        // Validate fields
        if (!woochimp_validate_email(jQuery('#woochimp_shortcode_subscription_email').val())) {
            jQuery('#woochimp_shortcode_subscription_email').css('border-color', 'red');
            form_ok = false;
        }
        if (jQuery('#woochimp_shortcode_subscription_first_name').length != 0) {
            if (!woochimp_validate_name(jQuery('#woochimp_shortcode_subscription_first_name').val())) {
                jQuery('#woochimp_shortcode_subscription_first_name').css('border-color', 'red');
                form_ok = false;
            }
        }
        if (jQuery('#woochimp_shortcode_subscription_last_name').length != 0) {
            if (!woochimp_validate_name(jQuery('#woochimp_shortcode_subscription_last_name').val())) {
                jQuery('#woochimp_shortcode_subscription_last_name').css('border-color', 'red');
                form_ok = false;
            }
        }

        // Consent checkbox displayed but no consent given
        if (jQuery('#woochimp_shortcode_field_consent_checkbox').length) {
            if (!jQuery('#woochimp_shortcode_field_consent_checkbox').is(':checked')) {
                form_ok = false;
            }
        }

        // Stop here if form is not ok
        if (!form_ok) {
            return;
        }

        // Hide fields
        jQuery(this).parent().parent().parent().children().each(function() {
            if (jQuery(this).find('#woochimp_shortcode_subscription_submit').length == 0) {
                jQuery(this).fadeOut(500);
            }
        });

        // Remove warning if previously set
        if (jQuery('#woochimp_shortcode_error').length != 0) {
            jQuery('#woochimp_shortcode_error').remove();
        }

        // Show progress
        if (jQuery('#woochimp_shortcode_subscription_loading').length == 0) {
            jQuery(this).parent().parent().parent().prepend('<div id="woochimp_shortcode_subscription_loading" class="woochimp_shortcode_loading"></div>');
            jQuery('#woochimp_shortcode_subscription_loading').delay(400).fadeIn(500);
        }

        // Send data to server
        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_subscribe_shortcode',
                'data': jQuery('#woochimp_registration_form_shortcode').serialize()
            },
            function(response) {
                var result = jQuery.parseJSON(response);

                // Remove loader
                jQuery('#woochimp_shortcode_subscription_loading').remove();

                if (result['error'] == 1) {

                    // Display warning
                    if (jQuery('#woochimp_shortcode_error').length == 0) {
                        jQuery('#woochimp_shortcode_subscription_submit').parent().parent().before('<tr id="woochimp_shortcode_error"><td>'+result['message']+'</td></tr>');
                        jQuery('#woochimp_shortcode_error').delay(500).fadeIn(500);
                    }

                    // Show fields again
                    jQuery('#woochimp_shortcode_subscription_submit').parent().parent().parent().children().each(function() {
                        if (jQuery(this).find('#woochimp_shortcode_subscription_submit').length == 0) {
                            jQuery(this).fadeIn(500);
                        }
                    });

                }
                else {

                    // Display success message
                    if (jQuery('#woochimp_shortcode_success').length == 0) {
                        jQuery('#woochimp_shortcode_subscription_submit').parent().parent().before('<tr id="woochimp_shortcode_success"><td>'+result['message']+'</td></tr>');
                        jQuery('#woochimp_shortcode_success').delay(500).fadeIn(500);
                    }

                    // Make button innactive
                    jQuery('#woochimp_shortcode_subscription_submit').attr('disabled', 'disabled');

                }

            }
        );
    }

    /**
     * Subscribe from widget
     */
    jQuery('#woochimp_widget_subscription_submit').click(function() {
        woochimp_widget_subscription_submit();
    });

    jQuery('.woochimp_widget_content').find('input[type="text"], input[type="email"]').keydown(function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            woochimp_widget_subscription_submit();
        }
    });

    function woochimp_widget_subscription_submit() {

        // Remove previously set errors
        jQuery('#woochimp_widget_subscription_email').css('border-color', '');
        jQuery('#woochimp_widget_subscription_first_name').css('border-color', '');
        jQuery('#woochimp_widget_subscription_last_name').css('border-color', '');

        var form_ok = true;

        // Validate fields
        if (!woochimp_validate_email(jQuery('#woochimp_widget_subscription_email').val())) {
            jQuery('#woochimp_widget_subscription_email').css('border-color', 'red');
            form_ok = false;
        }
        if (jQuery('#woochimp_widget_subscription_first_name').length != 0) {
            if (!woochimp_validate_name(jQuery('#woochimp_widget_subscription_first_name').val())) {
                jQuery('#woochimp_widget_subscription_first_name').css('border-color', 'red');
                form_ok = false;
            }
        }
        if (jQuery('#woochimp_widget_subscription_last_name').length != 0) {
            if (!woochimp_validate_name(jQuery('#woochimp_widget_subscription_last_name').val())) {
                jQuery('#woochimp_widget_subscription_last_name').css('border-color', 'red');
                form_ok = false;
            }
        }

        // Consent checkbox displayed but no consent given
        if (jQuery('#woochimp_widget_field_consent_checkbox').length) {
            if (!jQuery('#woochimp_widget_field_consent_checkbox').is(':checked')) {
                form_ok = false;
            }
        }

        // Stop here if form is not ok
        if (!form_ok) {
            return;
        }

        // Hide fields
        jQuery(this).parent().parent().parent().children().each(function() {
            if (jQuery(this).find('#woochimp_widget_subscription_submit').length == 0) {
                jQuery(this).fadeOut(500);
            }
        });

        // Remove warning if previously set
        if (jQuery('#woochimp_widget_error').length != 0) {
            jQuery('#woochimp_widget_error').remove();
        }

        // Show progress
        if (jQuery('#woochimp_widget_subscription_loading').length == 0) {
            jQuery(this).parent().parent().parent().prepend('<div id="woochimp_widget_subscription_loading" class="woochimp_widget_loading"></div>');
            jQuery('#woochimp_widget_subscription_loading').delay(400).fadeIn(500);
        }

        // Send data to server
        jQuery.post(
            ajaxurl,
            {
                'action': 'woochimp_subscribe_widget',
                'data': jQuery('#woochimp_registration_form_widget').serialize()
            },
            function(response) {
                var result = jQuery.parseJSON(response);

                // Remove loader
                jQuery('#woochimp_widget_subscription_loading').remove();

                if (result['error'] == 1) {

                    // Display warning
                    if (jQuery('#woochimp_widget_error').length == 0) {
                        jQuery('#woochimp_widget_subscription_submit').parent().parent().before('<tr id="woochimp_widget_error"><td>'+result['message']+'</td></tr>');
                        jQuery('#woochimp_widget_error').delay(500).fadeIn(500);
                    }

                    // Show fields again
                    jQuery('#woochimp_widget_subscription_submit').parent().parent().parent().children().each(function() {
                        if (jQuery(this).find('#woochimp_widget_subscription_submit').length == 0) {
                            jQuery(this).fadeIn(500);
                        }
                    });

                }
                else {

                    // Display success message
                    if (jQuery('#woochimp_widget_success').length == 0) {
                        jQuery('#woochimp_widget_subscription_submit').parent().parent().before('<tr id="woochimp_widget_success"><td>'+result['message']+'</td></tr>');
                        jQuery('#woochimp_widget_success').delay(500).fadeIn(500);
                    }

                    // Make button innactive
                    jQuery('#woochimp_widget_subscription_submit').attr('disabled', 'disabled');

                }

            }
        );
    }

    /**
     * Checkout checkbox setup
     */
    function checkout_checkbox_setup() {

        jQuery('#woochimp_user_preference').each(function() {
            if (!jQuery(this).is(':checked')) {
                toggle_required_groups('off');
                jQuery('#woochimp_checkout_groups').hide();
            }
        });

        jQuery('#woochimp_user_preference').click(function() {
            if (jQuery(this).is(':checked')) {
                toggle_required_groups('on');
                jQuery('#woochimp_checkout_groups').show();

            }
            else {
                toggle_required_groups('off');
                jQuery('#woochimp_checkout_groups').hide();
            }
        });
    }

    if (jQuery('.woocommerce #payment #woochimp_user_preference').length) {
        jQuery(document.body).on('updated_checkout', function() {
            checkout_checkbox_setup();
        });
    }

    checkout_checkbox_setup();

    /**
     * Handling 'required' attribute
     */
    function toggle_required_groups(action) {
        if (typeof woochimp_checkout_required_groups !== 'undefined') {
            jQuery(woochimp_checkout_required_groups).each(function(key, value) {
                jQuery('#woochimp_checkout_groups').find('.woochimp_checkout_field_' + value).each(function() {
                    if (action === 'on') {
                        jQuery(this).attr('required', true);
                    }
                    else {
                        jQuery(this).removeAttr('required');
                    }
                });
            });
        }
    }

});
