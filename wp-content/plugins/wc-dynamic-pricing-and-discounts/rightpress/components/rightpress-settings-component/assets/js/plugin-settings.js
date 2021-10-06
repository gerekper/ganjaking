/**
 * RightPress Plugin Settings Scripts
 */

jQuery(document).ready(function() {

    'use strict';

    /**
     * Toggle fields
     */
    jQuery('.rightpress-plugin-settings-has-conditions').each(function() {

        // Reference child field
        var child_field = jQuery(this);

        // Get all conditions for this setting
        var all_conditions = rightpress_plugin_settings.conditions[jQuery(this).prop('id')];

        // Iterate over all conditions
        jQuery.each(all_conditions, function(parent_key, conditions) {

            // Set up event listeners on parent
            jQuery(('#' + parent_key)).bind('keyup change', function() {

                // Initial state
                var conditions_pass = true;

                // Iterate over all conditions (we are doing this again for a reason!)
                jQuery.each(all_conditions, function(current_parent_key, current_conditions) {

                    // Reference parent field
                    var parent_field = jQuery(('#' + current_parent_key));

                    // Iterate over child-parent conditions
                    jQuery.each(current_conditions, function(condition_method, condition_value) {

                        // Is checked
                        if (condition_method === 'is_checked') {
                            if (!parent_field.is(':checked')) {
                                conditions_pass = false;
                                return false;
                            }
                        }
                        // Not empty
                        else if (condition_method === 'not_empty') {
                            if (!parent_field.val()) {
                                conditions_pass = false;
                                return false;
                            }
                        }
                        // Value
                        else if (condition_method === 'value') {
                            if (parent_field.val() !== condition_value) {
                                conditions_pass = false;
                                return false;
                            }
                        }
                    });

                    // Break after at least one failed condition
                    if (!conditions_pass) {
                        return false;
                    }
                });

                // Toggle field
                child_field.prop('disabled', !conditions_pass).closest('tr').css('display', (conditions_pass ? 'table-row' : 'none'));

            }).change();
        });
    });

    /**
     * Select2 for tag fields in settings
     */
    jQuery('select[multiple][data-select-2-tags]').each(function() {

        var config = {
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ';', ' '],
            placeholder: rightpress_plugin_settings_labels.select2_tags_placeholder,
            language: {
                noResults: function (params) {
                    return rightpress_plugin_settings_labels.select2_tags_no_results;
                }
            },
        };

        // Initialize Select2
        jQuery(this).select2(config);
    });

    /**
     * We are done by now, remove preloader
     */
    jQuery('#rightpress-plugin-settings-preloader').remove();

});
