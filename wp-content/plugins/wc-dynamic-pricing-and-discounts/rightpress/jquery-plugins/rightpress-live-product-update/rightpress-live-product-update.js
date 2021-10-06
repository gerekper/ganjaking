/**
 * RightPress Live Product Update
 */

(function () {

    'use strict';

    /**
     * Delay helper
     */
    var delay = (function(){

        var timers = {};

        return function(callback, ms, unique){
            clearTimeout(timers[unique]);
            timers[unique] = setTimeout(callback, ms);
        };
    })();

    /**
     * Register plugin
     */
    jQuery.fn.rightpress_live_product_update = function(params) {

        var self = this;
        var form = this.closest('.product').find('form.cart');

        // Unique id for each instance
        var unique = Math.random().toString(36).slice(2);

        // On input change
        form.find(':input').on('change keyup', function() {
            queue();
        });

        // Allow inputs to be attached on the fly
        form.on('rightpress_live_product_update_attach_input', function(event, element) {
            jQuery(element).find(':input').on('change keyup', function() {
                queue();
            });
        });

        // On variation select and our custom event
        form.on('found_variation, rightpress_live_product_update_trigger', function() {
            queue();
        });

        // Trigger now
        queue();

        /**
         * Make Ajax call
         */
        function call()
        {
            // Serialize form data
            var form_data = form.serialize();

            // Get product id
            var product_id = params.product_id !== undefined ? params.product_id : form.find('button[type="submit"][name="add-to-cart"]').val();

            // Add product id
            if (product_id) {
                form_data += (form_data !== '' ? '&' : '') + 'rightpress_reference_product_id=' + product_id;
            }

            // Compile a list of field names so that even empty fields (checkboxes, file uploads etc) are submitted
            form.find('input, textarea, select').each(function() {
                if (jQuery(this).is(':visible') && typeof jQuery(this).prop('name') !== 'undefined') {
                    form_data += (form_data !== '' ? '&' : '') + 'rightpress_complete_input_list[]=' + jQuery(this).prop('name');
                }
            });

            // Send request
            jQuery.ajax({
                type: 'POST',
                url: params.ajax_url,
                context: self,
                data: {
                    action: params.action,
                    data:   form_data
                },
                dataType: 'json',
                dataFilter: jQuery.rightpress.sanitize_json_response,
                beforeSend: params.before_send,
                success: params.response_handler
            });
        }

        /**
         * Queue call
         * Waits for 500 ms before actually executing, cancels any pending processes
         */
        function queue()
        {
            delay(function() {
                call();
            }, 500, unique);
        }


    };

}());
