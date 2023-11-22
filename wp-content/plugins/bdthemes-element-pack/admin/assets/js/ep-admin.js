jQuery(document).ready(function ($) {


    if (jQuery('.wrap').hasClass('element-pack-dashboard')) {

        // total activate
        function total_widget_status() {
            var total_widget_active_status = [];

            var totalActivatedWidgets = [];
            jQuery('#element_pack_active_modules_page input:checked').each(function () {
                totalActivatedWidgets.push(jQuery(this).attr('name'));
            });

            total_widget_active_status.push(totalActivatedWidgets.length);

            var totalActivated3rdparty = [];
            jQuery('#element_pack_third_party_widget_page input:checked').each(function () {
                totalActivated3rdparty.push(jQuery(this).attr('name'));
            });

            total_widget_active_status.push(totalActivated3rdparty.length);

            var totalActivatedExtensions = [];
            jQuery('#element_pack_elementor_extend_page input:checked').each(function () {
                totalActivatedExtensions.push(jQuery(this).attr('name'));
            });

            total_widget_active_status.push(totalActivatedExtensions.length);


            jQuery('#bdt-total-widgets-status').attr('data-value', total_widget_active_status);
            jQuery('#bdt-total-widgets-status-core').text(total_widget_active_status[0]);
            jQuery('#bdt-total-widgets-status-3rd').text(total_widget_active_status[1]);
            jQuery('#bdt-total-widgets-status-extensions').text(total_widget_active_status[2]);

            jQuery('#bdt-total-widgets-status-heading').text(total_widget_active_status[0] + total_widget_active_status[1] + total_widget_active_status[2]);

        }

        total_widget_status();

        jQuery('.element-pack-settings-save-btn').on('click', function () {
            setTimeout(function () {
                total_widget_status();
            }, 2000);
        });

        // end total active

        // modules
        var moduleUsedWidget = jQuery('#element_pack_active_modules_page').find('.ep-used-widget');
        var moduleUsedWidgetCount = jQuery('#element_pack_active_modules_page').find('.ep-options .ep-used').length;
        moduleUsedWidget.text(moduleUsedWidgetCount);
        var moduleUnusedWidget = jQuery('#element_pack_active_modules_page').find('.ep-unused-widget');
        var moduleUnusedWidgetCount = jQuery('#element_pack_active_modules_page').find('.ep-options .ep-unused').length;
        moduleUnusedWidget.text(moduleUnusedWidgetCount);

        // 3rd party
        var thirdPartyUsedWidget = jQuery('#element_pack_third_party_widget_page').find('.ep-used-widget');
        var thirdPartyUsedWidgetCount = jQuery('#element_pack_third_party_widget_page').find('.ep-options .ep-used').length;
        thirdPartyUsedWidget.text(thirdPartyUsedWidgetCount);
        var thirdPartyUnusedWidget = jQuery('#element_pack_third_party_widget_page').find('.ep-unused-widget');
        var thirdPartyUnusedWidgetCount = jQuery('#element_pack_third_party_widget_page').find('.ep-options .ep-unused').length;
        thirdPartyUnusedWidget.text(thirdPartyUnusedWidgetCount);


        // total widgets

        var dashboardChatItems = ['#bdt-db-total-status', '#bdt-db-only-widget-status', '#bdt-db-only-3rdparty-status', '#bdt-total-widgets-status'];

        dashboardChatItems.forEach(function ($el) {

            const ctx = jQuery($el);

            var $value = ctx.data('value');
            $value = $value.split(',');

            var $labels = ctx.data('labels');
            $labels = $labels.split(',');

            var $bg = ctx.data('bg');
            $bg = $bg.split(',');

            const data = {
                datasets: [{
                    data: $value,
                    backgroundColor: $bg,
                    borderWidth: 0,
                }],

            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    animation: {
                        duration: 3000,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    title: {
                        display: false,
                        text: ctx.data('label'),
                        fontSize: 16,
                        fontColor: '#333',
                    },
                    hover: {
                        mode: null
                    },

                }
            };

            if (window.myChart instanceof Chart) {
                window.myChart.destroy();
            }
            var myChart = new Chart(ctx, config);

        });

    }

    jQuery('.element-pack-notice.notice-error img').css({
        'margin-right': '8px',
        'vertical-align': 'middle'
    });

});