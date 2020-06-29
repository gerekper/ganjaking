(function ($) {
    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][page_background_color]', function (value) {
        value.bind(function (to) {
            $('.mo-page-bg-color').css('background-color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][header_background_color]', function (value) {
        value.bind(function (to) {
            $('.mo-header-bg-color').css('background-color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][header_text_color]', function (value) {
        value.bind(function (to) {
            $('.mo-header-text-color').css('color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][header_web_version_link_label]', function (value) {
        value.bind(function (to) {
            $('.mo-header-web-version-label').html(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][header_web_version_link_color]', function (value) {
        value.bind(function (to) {
            $('.mo-header-web-version-color').css('color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][header_text]', function (value) {
        value.bind(function (to) {
            var header_text = $('.mo-header-text');
            if (header_text.children().prop("tagName") != 'IMG') {
                header_text.text(to);
            }
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_before_main_content]', function (value) {
        value.bind(function (to) {
            to = $.parseHTML(to);
            $('.mo-before-main-content').html(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_after_main_content]', function (value) {
        value.bind(function (to) {
            to = $.parseHTML(to);
            $('.mo-after-main-content').html(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_background_color]', function (value) {
        value.bind(function (to) {
            $('.mo-content-background-color').css('background-color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_title_font_size]', function (value) {
        value.bind(function (to) {
            $('.mo-content-title-font-size').css('font-size', to + 'px');
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_body_font_size]', function (value) {
        value.bind(function (to) {
            $('.mo-content-body-font-size').css('font-size', to + 'px');
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_headline_color]', function (value) {
        value.bind(function (to) {
            // https://stackoverflow.com/a/25100304/2648410
            $('.mo-content-headline-color').each(function () {
                $(this).get(0).style.setProperty('color', to, 'important');
            });
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_text_color]', function (value) {
        value.bind(function (to) {
            // https://stackoverflow.com/a/25100304/2648410
            $('.mo-content-text-color').each(function () {
                $(this).get(0).style.setProperty('color', to, 'important');
            });
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_ellipsis_button_background_color]', function (value) {
        value.bind(function (to) {
            $('.mo-content-button-background-color').css('background-color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_ellipsis_button_text_color]', function (value) {
        value.bind(function (to) {
            $('.mo-content-button-text-color').css('color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_alignment]', function (value) {
        value.bind(function (to) {
            $('.mo-content-alignment').css('text-align', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_ellipsis_button_alignment]', function (value) {
        value.bind(function (to) {
            var cache = $('div.mo-content-button-alignment');
            cache.attr('align', to);
            cache.css('text-align', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][content_ellipsis_button_label]', function (value) {
        value.bind(function (to) {
            $('.mo-content-read-more-label').text(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_background_color]', function (value) {
        value.bind(function (to) {
            $('.mo-footer-bg-color').css('background-color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_text_color]', function (value) {
        value.bind(function (to) {
            $('.mo-footer-text-color').css('color', to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_font_size]', function (value) {
        value.bind(function (to) {
            $('.mo-footer-font-size').css('font-size', to + 'px');
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_copyright_line]', function (value) {
        value.bind(function (to) {
            to = $.parseHTML(to);
            $('.mo-footer-copyright-line').html(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_description]', function (value) {
        value.bind(function (to) {
            to = $.parseHTML(
                // convert newlines to br
                to.replace(/(?:\r\n|\r|\n)/g, '<br />')
            );
            $('.mo-footer-description').html(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_unsubscribe_line]', function (value) {
        value.bind(function (to) {
            $('.mo-footer-unsubscribe-line').text(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_unsubscribe_link_label]', function (value) {
        value.bind(function (to) {
            $('.mo-footer-unsubscribe-link-label').text(to);
        });
    });

    wp.customize(mailoptin_email_campaign_option_prefix + '[' + mailoptin_email_campaign_id + '][footer_unsubscribe_link_color]', function (value) {
        value.bind(function (to) {
            $('.mo-footer-unsubscribe-link-color').css('color', to);
        });
    });
})(jQuery);