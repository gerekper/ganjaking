(function ($) {

    function hide_all_controls(parent) {
        _.each(mailoptin_tab_control_config.style, function (value) {
            $('li[id$="' + value + '"]', parent).hide();
        });
        _.each(mailoptin_tab_control_config.general, function (value) {
            $('li[id$="' + value + '"]', parent).hide();
        });

        _.each(mailoptin_tab_control_config.advance, function (value) {
            $('li[id$="' + value + '"]', parent).hide();
        });
    }

    wp.customize.bind('ready', function () {

        if (typeof mailoptin_tab_control_config === "undefined") return;

        $('.mailoptin-toggle-control-tab').each(function () {
            var parent = $(this).parents('ul.customize-pane-child');

            var active_tab = $('.mailoptin-toggle-control-radio:checked', parent).val();

            hide_all_controls(parent);

            $('.mo-toggle-tab-wrapper', parent).hide();
            _.each(mailoptin_tab_control_config, function (value, key) {
                if (typeof mailoptin_tab_control_config[key] !== 'undefined') {
                    if (key === 'advance' && parent.is('[id*="mailoptin_campaign_settings_section"]') === false) return;
                    if (key === 'style' && parent.is('[id*="mailoptin_campaign_settings_section"]') === true) return;
                    $('.mo-toggle-tab-wrapper.mo-' + key, parent).show();
                }
            });

            _.each(mailoptin_tab_control_config[active_tab], function (value) {
                $('li[id$="' + value + '"]', parent).show();
            });

            $('input.mailoptin-toggle-control-radio', parent).on('click', function () {
                var parent = $(this).parents('ul.customize-pane-child');
                active_tab = this.value;
                hide_all_controls(parent);

                _.each(mailoptin_tab_control_config[active_tab], function (value) {
                    $('li[id$="' + value + '"]', parent).show();
                });
            });
        });

    });
})(jQuery);
