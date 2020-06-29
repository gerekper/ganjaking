(function ($) {
    'use strict';

    $(window).on('load', function () {

// --------------------- Rename optin theme ----------------------------- //

        $('body').on('click', '.panel-title.site-title', function () {
            jQuery.fancybox.open({
                src: '#mo-change-name-html',
                type: 'inline'
            });
        })
            .on('click', '#mosavetitle', function () {
                var inputField = $('#motitleinput');
                var title = inputField.val().trim();

                var _this = this;

                if (title === '') {
                    inputField.css("-webkit-box-shadow", "inset 0px 0px 0px 2px #f45a4a");
                    inputField.css("-moz-box-shadow", "inset 0px 0px 0px 2px #f45a4a");
                    inputField.css("box-shadow", "inset 0px 0px 0px 2px #f45a4a");
                } else {
                    var old_btn_value = $(this).attr('value');

                    $(this).attr('value', $(this).data('processing-label'));

                    var ajax_data = {
                        action: 'mailoptin_customizer_rename_optin',
                        title: title,
                        security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                    };

                    if (typeof mailoptin_optin_campaign_id !== 'undefined') {
                        ajax_data.optin_campaign_id = mailoptin_optin_campaign_id;
                    }

                    if (typeof mailoptin_email_campaign_id !== 'undefined') {
                        ajax_data.email_campaign_id = mailoptin_email_campaign_id;
                    }

                    $.post(ajaxurl, ajax_data, function () {
                        $(_this).attr('value', old_btn_value);
                        $('.panel-title.site-title').text(title);
                        $.fancybox.getInstance().close();
                    });
                }
            });


    });

})(jQuery);