(function ($) {
    // handles display of relevant email templates for select campaign type
    $(document).ready(function () {
        $('select#mo-email-newsletter-title').on('change', function (e) {
            var optionSelected = $("option:selected", this).val();
            // hide any displayed settings
            $('div[id^="notifType"]').hide();

            if (optionSelected != "...") {
                $('div#notifType_' + optionSelected).fadeIn();
            }
        });

        $('.mailoptin-email-template').on('click', function (e) {
            var campaign_title_obj = $('#mailoptin-add-campaign-title');
            // remove input field error on change.
            campaign_title_obj.change(function () {
                campaign_title_obj.removeClass('mailoptin-input-error');
            });

            if (!campaign_title_obj.val()) {
                campaign_title_obj.addClass('mailoptin-input-error');
                alert(mailoptin_globals.js_required_title);
                campaign_title_obj.focus();
            } else {
                campaign_title_obj.removeClass('mailoptin-input-error');
                $(".mailoptin-error").remove();
                $('.mailoptin-new-toolbar .mo-dash-spinner').css('visibility', 'visible');

                var ajaxData = {
                    action: 'mailoptin_create_email_campaign',
                    nonce: mailoptin_globals.nonce,
                    title: campaign_title_obj.val().trim(),
                    template: $(this).attr('data-email-template').trim(),
                    type: $(this).attr('data-campaign-type').trim()
                };

                $.post(ajaxurl, ajaxData, function (response) {
                        if (response.success && response.data.redirect) {
                            window.location.assign(response.data.redirect);
                        } else {
                            var error_msg = response.data ? response.data : '';
                            campaign_title_obj.after('<span class="mailoptin-error">' + error_msg + '</span>');
                            $('.mailoptin-new-toolbar .mo-dash-spinner').css('visibility', 'hidden');
                        }
                    }, 'json'
                );
            }
        });
    });
})(jQuery);