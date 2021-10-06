/**
 * WooCommerce Dynamic Pricing & Discounts - Promotion - Countdown Timer - Scripts
 */
jQuery(document).ready(function() {

    'use strict';

    /**
     * Initialize live product update
     */
    jQuery('.rp_wcdpd_promotion_countdown_timer_container').each(function() {

        // Get product id
        var product_id = jQuery(this).data('product_id');

        // Initialize live update
        jQuery(this).rightpress_live_product_update({

            // Params
            ajax_url:   rp_wcdpd_promotion_countdown_timer.ajaxurl,
            action:     'rp_wcdpd_promotion_countdown_timer_update',
            product_id: product_id,

            // Callback
            response_handler: function(response) {

                // Display timer
                if (typeof response === 'object' && typeof response.result !== 'undefined' && response.result === 'success' && response.display) {

                    // Remove outdated timer
                    if (typeof jQuery(this).data('rp-wcdpd-hash') !== 'undefined' && jQuery(this).data('rp-wcdpd-hash') !== response.hash) {
                        jQuery(this).html('');
                        jQuery(this).removeData('rp-wcdpd-hash');
                    }

                    // Display timer only if it's not displayed already
                    if (typeof jQuery(this).data('rp-wcdpd-hash') === 'undefined') {

                        // Update html
                        jQuery(this).data('rp-wcdpd-hash', response.hash);
                        jQuery(this).html(response.html);

                        // Initialize timer
                        initialize_timer(jQuery(this).find('.rp_wcdpd_promotion_countdown_timer'));

                        // Show timer
                        jQuery(this).slideDown();
                    }
                }
                // Hide timer
                else {
                    jQuery(this).slideUp();
                    jQuery(this).html('');
                    jQuery(this).removeData('rp-wcdpd-hash');
                }
            }
        });
    });

    /**
     * Initialize timer
     */
    function initialize_timer(element)
    {
        // Get end time in browser's time
        var end_time = new Date();
        end_time = new Date(end_time.getTime() + (element.data('seconds') * 1000));

        // Select elements
        var element_days    = element.find('.rp_wcdpd_promotion_countdown_timer_days_value');
        var element_hours   = element.find('.rp_wcdpd_promotion_countdown_timer_hours_value');
        var element_minutes = element.find('.rp_wcdpd_promotion_countdown_timer_minutes_value');
        var element_seconds = element.find('.rp_wcdpd_promotion_countdown_timer_seconds_value');

        // Initialize
        update_timer();
        var interval = setInterval(update_timer, 1000);

        /**
         * Update timer
         */
        function update_timer()
        {
            // Get remaining time
            var t = get_time_remaining(end_time);

            // Update elements
            element_days.html(('0' + t.days).slice(-2));
            element_hours.html(('0' + t.hours).slice(-2));
            element_minutes.html(('0' + t.minutes).slice(-2));
            element_seconds.html(('0' + t.seconds).slice(-2));

            // Clear interval when time runs up
            if (t.total <= 0) {
                clearInterval(interval);
                element.remove();
            }
        }
    }

    /**
     * Get time remaining
     */
    function get_time_remaining(end_time)
    {
        var t = Date.parse(end_time) - Date.parse(new Date());
        var seconds = Math.floor((t / 1000) % 60);
        var minutes = Math.floor((t / 1000 / 60) % 60);
        var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
        var days = Math.floor(t / (1000 * 60 * 60 * 24));

        return {
            'total':    t,
            'days':     days,
            'hours':    hours,
            'minutes':  minutes,
            'seconds':  seconds
        };
    }

});
