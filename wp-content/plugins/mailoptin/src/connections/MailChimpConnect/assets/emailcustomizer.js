(function (api, $) {
    "use strict";

    var mc = {};

    mc.add_spinner = function (placement) {
        var spinner_html = $('<img class="mo-spinner fetch-email-list" src="' + mailoptin_globals.admin_url + 'images/spinner.gif">');
        $(placement).after(spinner_html);
    };

    mc.remove_spinner = function () {
        $('.mo-spinner.fetch-email-list').remove();
    };

    mc.hide_show_group_select_chosen = function () {
        var groups_select_obj = $("select[data-customize-setting-link*='MailChimpConnect_groups'] option");

        if (groups_select_obj.length === 0) {
            $("div#customize-theme-controls li[id*='MailChimpConnect_groups']").hide()
        }
    };

    mc.fetch_groups = function () {

        $("select[data-customize-setting-link*='connection_email_list']").change(function (e) {
            var list_id = this.value;

            if ($("select[data-customize-setting-link*='connection_service']").val() !== 'MailChimpConnect') return;

            $("div#customize-theme-controls li[id*='MailChimpConnect_groups']").hide();

            mc.add_spinner(this);

            $.post(ajaxurl, {
                    action: 'mailoptin_customizer_fetch_mailchimp_segment',
                    list_id: list_id,
                    security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                },
                function (response) {
                    if (_.isObject(response) && 'success' in response && 'data' in response) {

                        var mailchimp_group_chosen = $("select[data-customize-setting-link*='MailChimpConnect_groups']");

                        mailchimp_group_chosen.html(response.data);

                        mailchimp_group_chosen.trigger('chosen:updated');

                        if (response.data !== '') {
                            $("div#customize-theme-controls li[id*='MailChimpConnect_groups']").show();
                        }

                        mc.remove_spinner();
                    }
                }
            );
        });
    };


    $(window).on('load', function () {
        mc.hide_show_group_select_chosen();
        mc.fetch_groups();
    });


})(wp.customize, jQuery);