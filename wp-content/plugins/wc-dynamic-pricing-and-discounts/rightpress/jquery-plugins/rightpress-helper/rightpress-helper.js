/**
 * RightPress Javascript Helper Functions
 */

(function () {

    'use strict';

    /**
     * Register functions
     */
    jQuery.extend({

        rightpress: {

            /**
             * Attempt to sanitize JSON response
             * For use as jQuery Ajax dataFilter
             * Response must start with {"result and end with ]}
             */
            sanitize_json_response: function(response) {

                try {

                    // Attempt to parse JSON
                    jQuery.parseJSON(response);

                    // Parsing succeeded
                    return response;
                }
                catch (e) {

                    // Attempt to fix malformed JSON string
                    var valid_response = response.match(/{"result.*]}/);

                    // Check if we were able to fix it
                    if (valid_response !== null) {
                        return valid_response[0];
                    }
                }
            },

            /**
             * Safely parse JSON Ajax response
             */
            parse_json_response: function (response, return_raw_data) {

                // Check if we need to return parsed object or potentially fixed raw data
                var return_raw_data = (typeof return_raw_data !== 'undefined') ?  return_raw_data : false;

                try {

                    // Attempt to parse data
                    var parsed = jQuery.parseJSON(response);

                    // Return appropriate value
                    return return_raw_data ? response : parsed;
                }
                catch (e) {

                    // Attempt to fix malformed JSON string
                    var regex = return_raw_data ? /{"result.*"}]}/ : /{"result.*"}/;
                    var valid_response = response.match(regex);

                    // Check if we were able to fix it
                    if (valid_response !== null) {
                        response = valid_response[0];
                    }
                }

                // Second attempt to parse response data
                return return_raw_data ? response : jQuery.parseJSON(response);
            },

            /**
             * Add nested object value
             */
            add_nested_object_value: function (object, path, value) {

                var last_key_index = path.length - 1;

                for (var i = 0; i < last_key_index; ++ i) {

                    var key = jQuery.isNumeric(path[i]) ? parseInt(path[i]) : path[i];

                    if (jQuery.isNumeric(path[i + 1])) {
                        if (typeof object[key] === 'undefined') {
                            object[key] = [];
                        }
                    }
                    else if (!(key in object)) {
                        object[key] = {};
                    }

                    object = object[key];
                }

                object[path[last_key_index]] = value;
            },

            /**
             * Nested object key existence check
             */
            object_key_check: function (object /*, key_1, key_2... */) {

                var keys = Array.prototype.slice.call(arguments, 1);
                var current = object;

                // Iterate over keys
                for (var i = 0; i < keys.length; i++) {

                    // Check if current key exists
                    if (typeof current[keys[i]] === 'undefined') {
                        return false;
                    }

                    // Check if all but last keys are for object
                    if (i < (keys.length - 1) && typeof current[keys[i]] !== 'object') {
                        return false;
                    }

                    // Go one step down
                    current = current[keys[i]];
                }

                // If we reached this point all keys from path
                return true;
            },

            /**
             * Clear field value
             */
            clear_field_value: function (field) {

                if (field.is('select')) {
                    field.prop('selectedIndex', 0);
                    if (field.hasClass('rightpress_select2')) {
                        field.val('').change();
                    }
                }
                else if (field.is(':radio, :checkbox')) {
                    field.removeAttr('checked');
                }
                else {
                    field.val('');
                }
            },

            /**
             * Check if field is multiselect
             */
            field_is_multiselect: function (field) {
                return (field.is('select') && typeof field.attr('multiple') !== 'undefined' && field.attr('multiple') !== false);
            },

            /**
             * Check if field is empty, i.e. does not contain text, is not checked, value is not selected etc.
             *
             * Accepts jQuery object with multiple elements for checkbox/radio sets and single element for all other input types
             *
             * TODO: REVIEW IF THIS IS OK, NEVER USED/TESTED THIS
             */
            field_is_empty: function (field) {

                // Multiple inputs - set of checkboxes or radio buttons
                if (field.length > 1) {

                    // No inputs can be checked
                    return !field.filter(':checked').length;
                }
                // No inputs
                else if (field.length < 1) {

                    // We should never end up here but lets treat this as empty just in case
                    return true;
                }
                // Single input
                else {

                    // Get field value
                    var value = field.val();

                    // Multiselect
                    if (jQuery.rightpress.field_is_multiselect(field)) {

                        // Default to empty array
                        value = value || [];

                        // No options can be selected
                        return !value.length;
                    }
                    // Radio or checkbox inputs
                    else if (field.is(':radio, :checkbox')) {

                        // Field must not be checked
                        return !field.is(':checked');
                    }
                    // Regular select fields and other inputs
                    else {

                        // Check if value is empty string or null
                        return (value === '' || value === null || value === undefined);
                    }
                }
            },

            /**
             * Get current page url with appended extra query var
             */
            get_current_url_with_query_var: function(key, value) {

                // Get current url
                var url = window.location.href;

                // Check if current url has query string
                var has_query = url.indexOf('?') > -1;

                // Get operator symbol
                var operator = has_query ? '&' : '?';

                // Append extra var if it does not exist yet
                // Note: This function currently does not check for differences in values
                if (!has_query || (url.indexOf(('?' + key + '=')) === -1 && url.indexOf(('&' + key + '=')) === -1)) {
                    url += operator + key + '=' + value;
                }

                // Return url
                return url;
            },

            /**
             * Serialize form including disabled inputs defined by selector
             */
            serialize_including_disabled: function(form, selector) {

                // Get disabled inputs and enable them
                var disabled = form.find(':input:disabled').filter(selector).removeAttr('disabled');

                // Serialize form
                var serialized = form.serialize();

                // Enable previously disabled inputs
                disabled.attr('disabled', 'disabled');

                // Return serialized data
                return serialized;
            }
    }

    });

}());
