(function ($) {

    $.fn.extend({
        animateOptin: function (animationName) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            this.addClass('MOanimated ' + animationName).one(animationEnd, function () {
                $(this).removeClass('MOanimated ' + animationName);
            });
        }
    });

    // --------------------- Design control ----------------------------- //

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][form_background_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-wrapper').css('background-color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][form_border_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-wrapper').css('border-color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][form_custom_css]', function (value) {
        value.bind(function (to) {
            // remove main custom css style rule to prevent if from overriding the below customizer version.
            $('#mo-optin-form-custom-css').remove();
            $("#mo-customizer-preview-custom-css").replaceWith($('<style id="mo-customizer-preview-custom-css">' + to + "</style>"));
        });
    });


    // --------------------- Headline control ----------------------------- //

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][headline]', function (value) {
        value.bind(function (to) {
            // replace every paragraph ending with a new line appended. needed so subsequent string replacement will work.
            var a = to.replace(/<\/p>/g, '<\/p>' + "\r\n");

            // extract content between paragraph tag and append space to it.
            var b = a.replace(/<p>(.+)<\/p>/g, '$1 ');

            $('.mo-optin-form-headline').html(b)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][headline_font_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-headline').css('color', to);
        });
    });


    // --------------------- Description control ----------------------------- //
    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][description]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-description').html(to)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][description_font_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-description').css('color', to);
        });
    });


    // --------------------- Note control ----------------------------- //

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][note]', function (value) {
        value.bind(function (to) {
            // replace every paragraph ending with a new line appended. needed so subsequent string replacement will work.
            var a = to.replace(/<\/p>/g, '<\/p>' + "\r\n");

            // extract content between paragraph tag and append space to it.
            var b = a.replace(/<p>(.+)<\/p>/g, '$1 ');

            $('.mo-optin-form-note .mo-note-content').html(b)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][note_font_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-note').css('color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][note_close_optin_onclick]', function (value) {
        value.bind(function (to) {
            var cache = $('.mo-optin-form-note .mo-note-content');
            if (to === true) {
                cache.addClass('mo-close-optin');
                cache.css('text-decoration', 'underline');
                cache.css('cursor', 'pointer');
            } else {
                cache.removeClass('mo-close-optin');
                cache.css('text-decoration', '');
                cache.css('cursor', '');
            }
        });
    });

    // --------------------- Configuration control ----------------------------- //

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][slidein_position]', function (value) {
        value.bind(function (to) {
            if (to == 'bottom_left') {
                $('.mo-optin-form-slidein').removeClass('mo-slidein-bottom_right').addClass('mo-slidein-bottom_left');
            }
            else {
                $('.mo-optin-form-slidein').removeClass('mo-slidein-bottom_left').addClass('mo-slidein-bottom_right');
            }
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][bar_position]', function (value) {
        value.bind(function (to) {
            if (to == 'bottom') {
                $('.mo-optin-form-bar').removeClass('mo-optin-form-bar-top').addClass('mo-optin-form-bar-bottom');
            }
            else {
                $('.mo-optin-form-bar').removeClass('mo-optin-form-bar-bottom').addClass('mo-optin-form-bar-top');
            }
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_close_button]', function (value) {
        value.bind(function (to) {
                $('a[rel="moOptin:close"]').toggle(!to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][remove_branding]', function (value) {
        value.bind(function (to) {
                $('div.mo-optin-powered-by').toggle(!to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_headline]', function (value) {
        value.bind(function (to) {
            if (to) {
                $('.mo-optin-form-headline').hide();
            }
            else {
                $('.mo-optin-form-headline').show();
            }
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_description]', function (value) {
        value.bind(function (to) {
            if (to) {
                $('.mo-optin-form-description').hide();
            }
            else {
                $('.mo-optin-form-description').show();
            }
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_note]', function (value) {
        value.bind(function (to) {
            if (to) {
                $('.mo-optin-form-note').hide();
            }
            else {
                $('.mo-optin-form-note').show();
            }
        });
    });

    // --------------------- Fields control ----------------------------- //

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_name_field]', function (value) {
        value.bind(function (to) {
            if (to) {
                $('.mo-optin-form-name-field').hide();
            }
            else {
                $('.mo-optin-form-name-field').show();
            }
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][name_field_placeholder]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-name-field').attr('placeholder', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][name_field_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-name-field').css('color', to)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][name_field_background]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-name-field').css('background-color', to)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][email_field_placeholder]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-email-field').attr('placeholder', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][email_field_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-email-field').css('color', to)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][email_field_background]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-email-field').css('background-color', to)
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][submit_button]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-submit-button').attr('value', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][submit_button_background]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-submit-button').css('background-color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][submit_button_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-submit-button').css('color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][cta_button]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-cta-button').attr('value', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][cta_button_background]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-cta-button').css('background-color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][cta_button_color]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-cta-button').css('color', to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][modal_effects]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-form-wrapper').animateOptin(to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_name_field]', function (value) {
        value.bind(function (to) {
            // add the flag indicator when "hide name field" is active
            var cache = $('.moOptinForm, .mo-optin-form-wrapper');
            cache.toggleClass('mo-has-email', to);
            cache.toggleClass('mo-has-name-email', !to);
        });
    });

    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][display_only_button]', function (value) {
        value.bind(function (to) {
            $('.mo-optin-fields-wrapper').toggle(!to);
            $('.mo-optin-form-cta-button').toggle(to);
            $('.mo-optin-form-cta-wrapper').toggle(to);
            $('.mo-mailchimp-interest-container').toggle(!to);
            // add the flag indicator when CTA button is active
            $('.moOptinForm').toggleClass('mo-cta-button-display', to).toggleClass('mo-cta-button-flag', to);
        });
    });

})(jQuery);