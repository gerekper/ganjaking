/**
 * Rules Interface Scripts
 */

jQuery(document).ready(function() {

    /**
     * Track duplicate actions
     */
    var duplicate_running = false;

    /**
     * Define rule editor elements
     */
    var rp_wcdpd_elements = {
        product_pricing: {
            children: {
                product_condition: {},
                bogo_product_condition: {},
                condition: {},
                quantity_range: {},
                group_product: {}
            }
        },
        cart_discounts: {
            children: {
                product_condition: {},
                condition: {}
            }
        },
        checkout_fees: {
            children: {
                product_condition: {},
                condition: {}
            }
        }
    };

    /**
     * Send serialized copy of data on submit in case we hit the max_input_vars limit
     */
    jQuery('form').has('.rp_wcdpd_settings').last().submit(function(e) {
        var form_data = jQuery(this).serialize();
        jQuery(this).find('input[name="rp_wcdpd_settings_serialized"]').remove();
        jQuery(this).prepend('<input type="hidden" name="rp_wcdpd_settings_serialized" />');
        jQuery(this).find('input[name="rp_wcdpd_settings_serialized"]').val(form_data);
        return true;
    });

    /**
     * Set up row state color coding
     */
    function set_up_row_state_color_coding(row)
    {

        // Rule selection method
        jQuery('.rp_wcdpd_settings select.rp_wcdpd_rule_selection_method').change(function() {
            update_row_state_color_coding(row);
        });

        // Rule exclusivity
        row.find('.rp_wcdpd_field_exclusivity').change(function() {
            update_row_state_color_coding(row);
        });

        // Trigger now
        update_row_state_color_coding(row);
    }

    /**
     * Update row state color coding
     */
    function update_row_state_color_coding(row)
    {

        // Reference row handle
        var handle = row.find('.rp_wcdpd_accordion_handle').first();

        // Reference inputs
        var rule_selection_method_value = jQuery('.rp_wcdpd_settings select.rp_wcdpd_rule_selection_method').val();
        var rule_exclusivity_value      = row.find('.rp_wcdpd_field_exclusivity').first().val();

        // Row disabled or all fields disabled
        if (rule_exclusivity_value === 'disabled' || rule_selection_method_value === 'disabled') {
            handle.css('border-left-color', '#cf4944');
        }
        // Row enabled
        else {
            handle.css('border-left-color', '#118238');
        }
    }

    /**
     * Iterate over elements and set up view
     */
    if (typeof rp_wcdpd_config === 'object') {
        jQuery.each(rp_wcdpd_elements, function(key, children) {

            // Set up all rules
            jQuery('#rp_wcdpd_' + key).each(function() {

                // Get config
                var config = jQuery.rightpress.object_key_check(rp_wcdpd_config, key) ? rp_wcdpd_config[key] : [];

                // Set up row
                set_up_parent(jQuery(this), key, config);

                // Set up header update
                var selectors = [];

                jQuery.each(['title', 'note', 'pricing_method', 'pricing_value', 'group_pricing_method', 'group_pricing_value', 'bogo_pricing_method', 'bogo_pricing_value', 'bogo_purchase_quantity', 'bogo_receive_quantity'], function(index, selector) {
                    selectors.push('#rp_wcdpd_' + key + ' .rp_wcdpd_' + key + '_field_' + selector);
                });

                jQuery('body').on('keyup change', selectors.join(), null, function() {
                    fix_row_header(jQuery(this), key);
                });
            });
        });
    }

    /**
     * Set up parent element
     */
    function set_up_parent(container, key, config)
    {
        // No rows configured yet?
        if (config.length === 0) {
            add_no_rows_notice(container, key);
        }
        // At least one row exists
        else {

            // Iterate over list of rows and add them
            jQuery.each(config, function(index, row_config) {
                add_row(key, row_config);
            });

            // Refresh accordion
            refresh_accordion(key);

            // Fix field identifiers
            fix_rows(key);

            // Fix field values
            fix_parent_values(key, false);

            // Row identifier
            var i = 0;

            // Iterate over rows
            jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper .rp_wcdpd_row').each(function() {

                var row = jQuery(this);

                // Row header fix
                fix_row_header(row, key);

                // Toggle product pricing specific settings areas
                if (key === 'product_pricing') {
                    toggle_product_pricing_settings(row.find('.rp_wcdpd_product_pricing_field_method'), key);
                }
                else {
                    toggle_cart_item_settings(row.find('.rp_wcdpd_' + key + '_field_pricing_method'), key);
                    fix_row_header(row, key);
                }

                // Initial condition fix
                jQuery.each(['bogo_product_condition', 'product_condition', 'condition'], function(index, alias) {

                    // Condition identifier
                    var j = 0;

                    // Iterate over conditions
                    row.find('.rp_wcdpd_' + alias).each(function() {

                        var condition = jQuery(this);
                        var skip_condition = false;

                        // Condition is disabled or no longer exists
                        jQuery.each(['_disabled', '_disabled_taxonomy', '_non_existent', '_non_existent_taxonomy'], function(flag_index, flag_type) {
                            if (typeof config[i][alias + 's'][j][flag_type] !== 'undefined') {
                                condition.find('.rp_wcdpd_condition_content, .rp_wcdpd_product_condition_content').html(get_template('condition' + flag_type));
                                skip_condition = true;
                            }
                        });

                        // Skip to next condition as current one is disabled
                        if (skip_condition) {
                            j++;
                            return true;
                        }

                        // Fields
                        fix_condition(key, alias, jQuery(this));

                        // Field identifiers
                        fix_child_element(key, (alias + 's'), jQuery(this), i, j);

                        // Field values
                        fix_child_values(key, false, alias, row, i, jQuery(this), j);

                        // Fix meta and coupon conditions (correct values were not present when it was first run)
                        toggle_condition_fields(key, alias, jQuery(this), jQuery(this).find('.rp_wcdpd_' + key + '_' + alias + '_type').val());

                        // Increment condition identifier
                        j++;
                    });
                });

                // Initial group product fix
                var j = 0;

                row.find('.rp_wcdpd_group_product').each(function() {

                    var group_product = jQuery(this);
                    var skip_group_product = false;

                    // Group product condition  is disabled or no longer exists
                    jQuery.each(['_disabled', '_disabled_taxonomy', '_non_existent', '_non_existent_taxonomy'], function(flag_index, flag_type) {
                        if (typeof config[i]['group_products'][j][flag_type] !== 'undefined') {
                            group_product.find('.rp_wcdpd_group_product_content').html(get_template('condition' + flag_type));
                            skip_group_product = true;
                        }
                    });

                    // Skip to next group product condition as current one is disabled
                    if (skip_group_product) {
                        j++;
                        return true;
                    }

                    // Fix group product
                    fix_group_product(key, jQuery(this));

                    // Increment group product condition identifier
                    j++;
                });

                // Increment row identifier
                i++;
            });
        }

        // Render add row button
        append(container, 'add_row')

        // Bind click action
        jQuery('#rp_wcdpd_add_row button').click(function() {
            jQuery(this).prop('disabled', true);
            add_row(key, false);
            refresh_accordion(key);
            jQuery(this).prop('disabled', false);
        });
    }

    /**
     * Add no rows notice
     */
    function add_no_rows_notice(selector, key)
    {
        prepend(selector, 'no_rows');
    }

    /**
     * Remove no rows notice
     */
    function remove_no_rows_notice(key)
    {
        jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_no_rows').remove();
    }

    /**
     * Add wrapper
     */
    function add_wrapper(key)
    {
        // Make sure we don't have one yet before proceeding
        if (jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper').length === 0) {

            // Add wrapper
            prepend('#rp_wcdpd_' + key, 'rule_wrapper', null);

            // Reference form
            var form = jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper').closest('form');

            // Make it sortable accordion
            jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper').accordion({
                header: '> div > div.rp_wcdpd_accordion_handle',
                icons: false,
                collapsible: true,
                heightStyle: 'content',
                active: false
            }).sortable({
                handle: '.rp_wcdpd_row_sort_handle',
                axis:   'y',
                stop: function(event, ui) {
                    fix_rows(key);
                }
            });
        }
    }

    /**
     * Remove wrapper
     */
    function remove_wrapper(key)
    {
        jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper').remove();
    }

    /**
     * Add one row
     */
    function add_row(key, config)
    {
        var selector = '#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper';

        // Add wrapper
        add_wrapper(key);

        // Make sure we don't have the "Nothing to display" notice
        remove_no_rows_notice(key);

        // Add row element
        append(selector, 'row', null);

        // Select current row
        var row = jQuery(selector).children().last();
        var row_key = jQuery(selector).children().length - 1;

        // Fix identifiers, values and visibility
        if (config === false) {
            fix_rows(key);
            fix_parent_values(key, true, row, row_key);
        }

        // Set up child elements
        jQuery.each(rp_wcdpd_elements[key].children, function(type) {
            set_up(key, type + 's', row, row_key, config);
        });

        // Add duplicate event handler
        add_duplicate_event_handler(key, row);

        // Handle delete action
        jQuery('#rp_wcdpd_' + key + ' .rp_wcdpd_row_remove_handle').last().click(function() {
            remove_row(key, jQuery(this).closest('.rp_wcdpd_row'));
        });

        // Display correct settings area for product pricing rules
        jQuery('#rp_wcdpd_product_pricing .rp_wcdpd_product_pricing_field_method').last().on('change', function() {
            toggle_product_pricing_settings(jQuery(this), key);
        });
        if (config === false) {
            toggle_product_pricing_settings(jQuery('#rp_wcdpd_product_pricing .rp_wcdpd_product_pricing_field_method').last(), key);
        }

        // Show or hide cart item settings for cart discount and checkout fee rules
        if (key === 'cart_discounts' || key === 'checkout_fees') {
            row.find('.rp_wcdpd_' + key + '_field_pricing_method').on('change', function() {
                toggle_cart_item_settings(jQuery(this), key);
            });
            if (config === false) {
                toggle_cart_item_settings(row.find('.rp_wcdpd_' + key + '_field_pricing_method'), key);
                fix_row_header(row, key);
            }
        }

        // Exclude field in header from activating panel change
        row.find('.rp_wcdpd_field_exclusivity').on('click', function() {
            event.stopPropagation();
            return false;
        });

        // Focus on first important field
        if (config === false) {
            var focus_selector = (key === 'product_pricing' ? '.rp_wcdpd_product_pricing_field_note' : '.rp_wcdpd_' + key + '_field_title');
            row.find(focus_selector).focus();
        }
    }

    /**
     * Add duplicate event handler
     */
    function add_duplicate_event_handler(key, row)
    {
        row.find('.rp_wcdpd_row_duplicate_handle').on('click', function() {

            // Prevent accordion from opening/closing
            event.stopPropagation();

            // Prevent multiple clicks
            if (duplicate_running) {
                return;
            }
            duplicate_running = true;

            // Duplicate row
            duplicate_row(key, jQuery(this).closest('.rp_wcdpd_row'));

            // Prevent multiple clicks
            setTimeout(function(){
                duplicate_running = false;
            }, 1000);
        });
    }

    /**
     * Remove duplicate event handler
     */
    function remove_duplicate_event_handler(key, row)
    {
        row.find('.rp_wcdpd_row_duplicate_handle').off('click');
    }

    /**
     * Duplicate one row
     */
    function duplicate_row(key, row)
    {
        // Select wrapper
        var wrapper = row.closest('#rp_wcdpd_rule_wrapper');

        // Get original row and row key
        var original_row = row;
        var original_row_key = row.index();

        // Get new row key
        var row_key = wrapper.children().length;

        // Start config mockup
        var config = {};
        var multiselect_options = {};

        // Iterate over all form elements and add values to config mockup
        original_row.find('input, select').each(function() {

            // Skip hidden fields
            if (jQuery(this).is(':disabled') || jQuery(this).attr('type') === 'hidden') {
                return;
            }

            // Get name parts
            var name_parts = jQuery(this).prop('name').replace('rp_wcdpd_settings[' + key + '][' + original_row_key + ']', '').replace('[]', '').slice(1, -1).split('][');

            // Add value to config mockup object
            jQuery.rightpress.add_nested_object_value(config, name_parts, jQuery(this).val());

            // Get multiselect field options
            if (jQuery.rightpress.field_is_multiselect(jQuery(this))) {
                var current_options = [];

                jQuery(this).find('option').each(function() {
                    current_options.push({
                        id: jQuery(this).prop('value'),
                        text: jQuery(this).text()
                    });
                });

                if (current_options.length > 0) {
                    jQuery.rightpress.add_nested_object_value(multiselect_options, name_parts, current_options);
                }
            }
        });

        // Add new row
        add_row(key, config);

        // Refresh accordion
        refresh_accordion(key);

        // Fix field identifiers
        fix_rows(key);

        // Select new row
        var row = wrapper.children().last();

        // Convert config mockup to full config mockup
        var top_level_config = {};
        jQuery.rightpress.add_nested_object_value(top_level_config, [key, row_key], config);
        var top_level_multiselect_options = {};
        jQuery.rightpress.add_nested_object_value(top_level_multiselect_options, [key, row_key], multiselect_options);

        // Fix field values
        fix_parent_values(key, false, row, row_key, top_level_config, top_level_multiselect_options);

        // Initial condition fix
        jQuery.each(['bogo_product_condition', 'product_condition', 'condition'], function(index, alias) {

            // Iterate over conditions
            row.find('.rp_wcdpd_' + alias).each(function() {

                // Fix condition
                fix_condition(key, alias, jQuery(this));

                // Fix condition field values
                fix_child_values(key, false, alias, row, row_key, jQuery(this), jQuery(this).index(), top_level_config, top_level_multiselect_options);

                // Fix condition again after adding values
                fix_condition(key, alias, jQuery(this));
            });
        });

        // Initial group product fix
        row.find('.rp_wcdpd_group_product').each(function() {
            fix_group_product(key, jQuery(this));
        });

        // Row header fix
        fix_row_header(row, key);

        // Other fixes
        if (key === 'product_pricing') {
            toggle_product_pricing_settings(row.find('.rp_wcdpd_product_pricing_field_method').last(), key);
        }
        else if (key === 'cart_discounts' || key === 'checkout_fees') {
            toggle_cart_item_settings(row.find('.rp_wcdpd_' + key + '_field_pricing_method'), key);
            fix_row_header(row, key);
        }
    }

    /**
     * Remove one row
     */
    function remove_row(key, row)
    {
        // Last row? Remove the entire wrapper and add "Nothing to display"
        if (row.closest('#rp_wcdpd_rule_wrapper').children().length < 2) {
            remove_wrapper(key);
            add_no_rows_notice('#rp_wcdpd_' + key, key)
        }

        // Remove single row and fix ids
        else {
            row.remove();
            fix_rows(key);
        }
    }

    /**
     * Fix attributes
     */
    function fix_rows(key)
    {
        var i = 0;  // Row identifier
        var j = 0;  // Child element identifier, e.g. conditions within a give row

        // Iterate over rows
        jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper .rp_wcdpd_row').each(function() {

            var row = jQuery(this);
            var element_wrappers = [];

            // Fix conditions etc
            jQuery.each(rp_wcdpd_elements[key].children, function(type) {

                var type_plural = type + 's';

                // Check if we have elements of this type for this row and handle them
                row.find('.rp_wcdpd_row_content_' + type_plural + '_row').each(function() {

                    element_wrappers.push(jQuery(this));

                    // Iterate over elements of this type of current row
                    jQuery(this).find('.rp_wcdpd_' + type + '_wrapper .rp_wcdpd_' + type).each(function() {

                        // Fix child element
                        fix_child_element(key, type_plural, jQuery(this), i, j);

                        // Increment element identifier
                        j++;
                    });

                    // Reset element identifier
                    j = 0;
                });
            });

            // Iterate over all field elements of this element
            jQuery(this).find('input, select, label').each(function() {

                var current_form_element = jQuery(this);

                // Do not touch child elements (already sorted above)
                if (element_wrappers.length > 0) {

                    var proceed = true;

                    jQuery.each(element_wrappers, function(index, value) {
                        if (jQuery.contains(value[0], current_form_element[0])) {
                            proceed = false;
                            return true;
                        }
                    });

                    if (!proceed) {
                        return true;
                    }
                }

                // Attribute id
                if (typeof jQuery(this).prop('id') !== 'undefined' && jQuery(this).prop('id')) {
                    var new_value = jQuery(this).prop('id').replace(/(\{i\}|\d+)?$/, i);
                    jQuery(this).prop('id', new_value);
                }

                // Attribute name
                if (typeof jQuery(this).prop('name') !== 'undefined') {
                    var new_value = jQuery(this).prop('name').replace(new RegExp('rp_wcdpd_settings\\[' + key + '\\]\\[(\\{i\\}|\\d+)\\]?'), 'rp_wcdpd_settings[' + key + '][' + i + ']');
                    jQuery(this).prop('name', new_value);
                }

                // Attribute for
                if (typeof jQuery(this).prop('for') !== 'undefined' && jQuery(this).prop('for').length) {
                    var new_value = jQuery(this).prop('for').replace(/(\{i\}|\d+)?$/, i);
                    jQuery(this).prop('for', new_value);
                }
            });

            // Increment row identifier
            i++;
        });
    }

    /**
     * Fix child element identifiers
     */
    function fix_child_element(key, type_plural, element, i, j)
    {
        // Iterate over all field elements of current element
        element.find('input, select').each(function() {

            // Attribute id
            if (typeof jQuery(this).prop('id') !== 'undefined') {
                var new_value = jQuery(this).prop('id').replace(/_(\{i\}|\d+)?_/, '_' + i + '_').replace(/(\{j\}|\d+)?$/, j);
                jQuery(this).prop('id', new_value);
            }

            // Attribute name
            if (typeof jQuery(this).prop('name') !== 'undefined') {
                var new_value = jQuery(this).prop('name').replace(new RegExp('rp_wcdpd_settings\\[' + key + '\\]\\[(\\{i\\}|\\d+)\\]?'), 'rp_wcdpd_settings[' + key + '][' + i + ']').replace(new RegExp('\\[' + type_plural + '\\]\\[(\\{j\\}|\\d+)\\]?'), '[' + type_plural + '][' + j + ']');
                jQuery(this).prop('name', new_value);
            }
        });
    }

    /**
     * Fix parent field values
     */
    function fix_parent_values(key, is_new, row, row_key, config_override, multiselect_options_override)
    {
        // Maybe override configuration values
        var config = typeof config_override !== 'undefined' ? config_override : rp_wcdpd_config;
        var multiselect_options = typeof multiselect_options_override !== 'undefined' ? multiselect_options_override : rp_wcdpd_multiselect_options;

        // Row identifier
        var i = typeof row_key !== 'undefined' ? row_key : 0;

        // Get rows to fix values for
        var rows = typeof row !== 'undefined' ? [row] : jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper .rp_wcdpd_row');

        // Iterate over rows
        jQuery.each(rows, function() {

            var row = jQuery(this);

            // Iterate over all field elements of this row except child elements
            jQuery(this).find('input:not(.rp_wcdpd_child_element_field), select:not(.rp_wcdpd_child_element_field)').each(function() {

                // Get field key
                var field_key = jQuery(this).prop('id').replace(new RegExp('^rp_wcdpd_' + key + '_'), '').replace(/(_\d+)?$/, '');

                // Select options in select fields
                if (jQuery(this).is('select')) {
                    if (!is_new && config !== false && jQuery.rightpress.object_key_check(config, key, i, field_key) && config[key][i][field_key]) {
                        if (jQuery.rightpress.field_is_multiselect(jQuery(this))) {
                            if (jQuery.rightpress.object_key_check(multiselect_options, key, i, field_key) && typeof multiselect_options[key][i][field_key] === 'object') {
                                for (var k = 0; k < config[key][i][field_key].length; k++) {
                                    var all_options = multiselect_options[key][i][field_key];
                                    var current_option_key = config[key][i][field_key][k];

                                    for (var l = 0; l < all_options.length; l++) {
                                        if (jQuery.rightpress.object_key_check(all_options, l, 'id') && all_options[l]['id'] == current_option_key) {
                                            var current_option_label = all_options[l]['text'];
                                            jQuery(this).append(jQuery('<option></option>').attr('value', current_option_key).prop('selected', true).text(current_option_label));
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            jQuery(this).val(config[key][i][field_key]);
                        }
                    }
                }

                // Add value for text input fields
                else if (jQuery(this).is('input')) {
                    if (!is_new && config !== false && jQuery.rightpress.object_key_check(config, key, i, field_key)) {
                        jQuery(this).prop('value', config[key][i][field_key]);
                    }
                    else {
                        jQuery(this).removeAttr('value');
                    }
                }

                // Initialize Select2 multiselect
                if (jQuery(this).hasClass('rp_wcdpd_select2_multiselect') && !jQuery(this).data('select2')) {
                    initialize_select2_multiselect(key, jQuery(this));
                }

                // Initialize Select2 grouped
                if (jQuery(this).hasClass('rp_wcdpd_select2_grouped') && !jQuery(this).data('select2')) {
                    jQuery(this).rightpress_grouped_select2();
                }
            });

            // Fix child values
            if (!is_new) {
                jQuery.each(rp_wcdpd_elements[key].children, function(type) {
                    fix_child_values(key, false, type, row, i, null, null, config_override, multiselect_options_override);
                });
            }

            // Increment row identifier
            i++;
        });
    }

    /**
     * Set up child elements, e.g. conditions for one row
     */
    function set_up(key, type, row, row_key, config)
    {
        var type_singular = type.replace(/s$/, '');

        // No existing children of given type
        if (config === false || typeof config !== 'object' || config.length < 1 || typeof config[type] !== 'object' || config[type].length < 1) {
            add_no(key, type, row);
        }
        // Set up existing children of given type
        else {

            jQuery.each(config[type], function(index, child_config) {
                add(key, type_singular, row, row_key, config);
            });
        }

        // Bind click action
        row.find('.rp_wcdpd_add_' + type_singular + ' button').click(function() {
            jQuery(this).prop('disabled', true);
            add(key, type_singular, row, row_key, false);
            jQuery(this).prop('disabled', false);
        });
    }

    /**
     * Add no child elements (e.g. conditions) notice
     */
    function add_no(key, type, row)
    {
        if (!row.find('.rp_wcdpd_row_content_' + type + '_row .rp_wcdpd_inner_wrapper .rp_wcdpd_no_' + type).length) {
            prepend(row.find('.rp_wcdpd_row_content_' + type + '_row .rp_wcdpd_inner_wrapper'), 'no_' + type);
        }
    }

    /**
     * Remove no child elements (e.g. conditions) notice
     */
    function remove_no(key, type, row)
    {
        row.find('.rp_wcdpd_no_' + type).remove();
    }

    /**
     * Add one child element, e.g. condition
     */
    function add(key, type, row, row_key, config)
    {
        // Add wrapper
        add_child_wrapper(key, type, row);

        // Make sure we don't have the no child elements notice
        remove_no(key, type + 's', row);

        // Add element
        append(row.find('.rp_wcdpd_' + type + '_wrapper'), type, null);

        // Select current row
        var child_row = row.find('.rp_wcdpd_' + type).last();
        var child_row_key = row.find('.rp_wcdpd_' + type).length - 1;

        // Fix identifiers, values and visibility on newly added item
        if (config === false) {

            // Fix fields
            fix_rows(key);
            fix_child_values(key, true, type, row, row_key, child_row, child_row_key);

            // Fix condition
            jQuery.each(['bogo_product_condition', 'product_condition', 'condition'], function(index, alias) {
                if (type === alias) {

                    // Fix condition fields
                    fix_condition(key, alias, child_row);

                    // Fix condition field identifiers
                    fix_child_element(key, (alias + 's'), child_row, row_key, child_row_key);

                    // Fix condition field values
                    fix_child_values(key, true, alias, row, row_key, child_row, child_row_key);
                }
            });

            // Other specific fixes by type
            if (type === 'group_product') {
                fix_group_product(key, child_row);
            }
            else if (type === 'quantity_range') {
                toggle_volume_pricing_methods(child_row, key);
            }
        }

        // Handle delete action
        row.find('.rp_wcdpd_' + type + '_remove_handle').last().click(function() {
            remove(key, type, jQuery(this).closest('.rp_wcdpd_' + type));
        });

        // Focus on first important field
        if (config === false) {

            var focus_selector = null;

            if (type === 'condition') {
                // May be distracting, disabled
                // focus_selector = '.rp_wcdpd_' + key + '_condition_date:enabled';
            }
            else if (type === 'product_condition') {
                focus_selector = '.rp_wcdpd_' + key + '_product_condition_products:enabled';
            }
            else if (type === 'bogo_product_condition') {
                focus_selector = '.rp_wcdpd_' + key + '_bogo_product_condition_products:enabled';
            }
            else if (type === 'quantity_range') {
                focus_selector = '.rp_wcdpd_' + key + '_quantity_range_from:enabled';
            }
            else if (type === 'group_product') {
                focus_selector = '.rp_wcdpd_' + key + '_group_product_quantity:enabled';
            }


            if (focus_selector !== null) {
                child_row.find(focus_selector).focus();
            }
        }
    }

    /**
     * Remove one child element, e.g. condition
     */
    function remove(key, type, element)
    {
        var row = element.closest('.rp_wcdpd_row');

        // Last element? Remove the entire wrapper and add no child elements notice
        if (row.find('.rp_wcdpd_' + type + '_wrapper').children().length < 2) {
            remove_child_wrapper(key, type, row);
            add_no(key, type + 's', row);
        }

        // Remove single element and fix ids
        else {
            element.remove();
            fix_rows(key);
        }
    }

    /**
     * Fix child field values
     */
    function fix_child_values(key, is_new, type, row, row_key, child_row, child_row_key, config_override, multiselect_options_override)
    {
        // Maybe override configuration values
        var config = typeof config_override !== 'undefined' ? config_override : rp_wcdpd_config;
        var multiselect_options = typeof multiselect_options_override !== 'undefined' ? multiselect_options_override : rp_wcdpd_multiselect_options;

        var type_plural = type + 's';

        // Row identifiers
        var i = row_key;
        var j = typeof child_row_key !== 'undefined' && child_row_key !== null ? child_row_key : 0;

        // Get rows to fix values for
        var rows = typeof child_row !== 'undefined' && child_row !== null ? [child_row] : row.find('.rp_wcdpd_' + type + '_wrapper .rp_wcdpd_' + type);

        // Iterate over child rows
        jQuery.each(rows, function() {

            // Iterate over all field elements of current element
            jQuery(this).find('input, select').each(function() {

                // Get field key
                var field_key = jQuery(this).prop('id').replace(new RegExp('^rp_wcdpd_' + key + '_' + type_plural + '_'), '').replace(/^(\d+_)?/, '').replace(/(_\d+)?$/, '');

                // Select options in select fields
                if (jQuery(this).is('select')) {
                    if (!is_new && config !== false && jQuery.rightpress.object_key_check(config, key, i, type_plural, j, field_key) && config[key][i][type_plural][j][field_key]) {
                        if (jQuery.rightpress.field_is_multiselect(jQuery(this))) {
                            if (jQuery.rightpress.object_key_check(multiselect_options, key, i, type_plural, j) && typeof multiselect_options[key][i][type_plural][j][field_key] === 'object') {

                                var all_options = multiselect_options[key][i][type_plural][j][field_key];
                                var multiselect_options_html = '';

                                for (var k = 0; k < config[key][i][type_plural][j][field_key].length; k++) {

                                    var current_option_key = config[key][i][type_plural][j][field_key][k];

                                    for (var l = 0; l < all_options.length; l++) {
                                        if (jQuery.rightpress.object_key_check(all_options, l, 'id') && all_options[l]['id'] == current_option_key) {
                                            multiselect_options_html += '<option value="' + current_option_key + '" selected="selected">' + all_options[l]['text'] + '</option>';
                                        }
                                    }
                                }

                                if (multiselect_options_html !== '') {
                                    jQuery(this).append(multiselect_options_html);
                                }
                            }
                        }
                        else {
                            jQuery(this).val(config[key][i][type_plural][j][field_key]);
                        }
                    }
                }

                // Add value for text input fields
                else if (typeof jQuery(this).prop('value') !== 'undefined' /*&& jQuery(this).prop('value') === '{value}'*/) {
                    if (!is_new && config !== false && jQuery.rightpress.object_key_check(config, key, i, type_plural, j, field_key)) {
                        jQuery(this).prop('value', config[key][i][type_plural][j][field_key]);
                    }
                    else {
                        jQuery(this).removeAttr('value');
                    }
                }

                // Select2 setup
                if (!jQuery(this).data('select2')) {

                    // Multiselect
                    if (jQuery(this).hasClass('rp_wcdpd_select2_multiselect')) {
                        initialize_select2_multiselect(key, jQuery(this), type);
                    }
                    // Grouped
                    else if (jQuery(this).hasClass('rp_wcdpd_select2_grouped')) {
                        jQuery(this).rightpress_grouped_select2();
                    }
                }
            });

            // Increment element identifier
            j++;
        });
    }

    /**
     * Add wrapper for child elements, e.g. conditions
     */
    function add_child_wrapper(key, type, row)
    {
        // Make sure we don't have one yet before proceeding
        if (row.find('.rp_wcdpd_' + type + '_wrapper').length === 0) {

            // Add wrapper
            prepend(row.find('.rp_wcdpd_row_content_' + type + 's_row .rp_wcdpd_inner_wrapper'), type + '_wrapper', null);

            // Make it sortable
            row.find('.rp_wcdpd_' + type + '_wrapper').sortable({
                axis:       'y',
                handle:     '.rp_wcdpd_' + type + '_sort_handle',
                opacity:    0.7,
                stop: function(event, ui) {

                    // Remove styles added by jQuery UI
                    jQuery(this).find('.rp_wcdpd_' + type).each(function() {
                        jQuery(this).removeAttr('style');
                    });

                    // Fix ids, names etc
                    fix_rows(key);
                }
            });
        }
    }

    /**
     * Remove child element wrapper
     */
    function remove_child_wrapper(key, type, row)
    {
        row.find('.rp_wcdpd_' + type + '_wrapper').remove();
    }

    /**
     * Fix condition
     */
    function fix_condition(key, alias, element)
    {
        // Get current condition type
        var condition_type = element.find('.rp_wcdpd_' + key + '_' + alias + '_type').val();

        // Condition type
        element.find('.rp_wcdpd_' + key + '_' + alias + '_type').change(function() {
            condition_type = element.find('.rp_wcdpd_' + key + '_' + alias + '_type').val();
            toggle_condition_fields(key, alias, element, condition_type);
        });
        toggle_condition_fields(key, alias, element, condition_type);
    }

    /**
     * Toggle visibility of condition fields
     */
    function toggle_condition_fields(key, alias, element, condition_type)
    {
        // Reference wrapper and row
        var wrapper = element.find('.rp_wcdpd_' + alias + '_setting_fields_wrapper');
        var row = element.closest('.rp_wcdpd_row');

        // Make sure we don't have required set of fields yet
        if (!wrapper.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type).length) {

            // Clear wrapper
            wrapper.html('');

            // Add condition fields
            append(wrapper, (alias + '_setting_fields_' + condition_type), null);

            // Field identifiers
            fix_child_element(key, (alias + 's'), element, row.index(), element.index());

            // Field values
            fix_child_values(key, true, alias, row, row.index(), element, element.index());

            // Fix meta field condition
            element.find('.rp_wcdpd_' + key + '_' + alias + '_method').change(function() {
                condition_type = element.find('.rp_wcdpd_' + key + '_' + alias + '_type').val();
                fix_meta_field_condition(key, alias, element, condition_type);
            });

            // Fix coupons applied condition
            element.find('.rp_wcdpd_' + key + '_' + alias + '_method').change(function() {
                condition_type = element.find('.rp_wcdpd_' + key + '_' + alias + '_type').val();
                fix_coupons_applied_condition(key, alias, element, condition_type);
            });

            // Date and time pickers
            element.find('.rp_wcdpd_date').datetimepicker(rp_wcdpd_datetimepicker_date_config.x);
            element.find('.rp_wcdpd_time').datetimepicker(rp_wcdpd_datetimepicker_time_config.x);
            element.find('.rp_wcdpd_datetime').datetimepicker(rp_wcdpd_datetimepicker_datetime_config.x);
            jQuery.datetimepicker.setLocale(rp_wcdpd_datetimepicker_locale.x);
        }

        // Fix meta field condition
        fix_meta_field_condition(key, alias, element, condition_type);

        // Fix coupons applied condition
        fix_coupons_applied_condition(key, alias, element, condition_type);
    }

    /**
     * Fix fields of meta field condition
     */
    function fix_meta_field_condition(key, alias, element, condition_type)
    {
        // Only proceed if condition type is meta field
        if (condition_type !== 'customer__meta' && condition_type !== 'product_property__meta') {
            return;
        }

        // Get current method
        var current_method = element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type + ' .rp_wcdpd_' + key + '_' + alias + '_method').val();

        // Reference text field
        var text_field = element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type + ' .rp_wcdpd_' + key + '_' + alias + '_text');

        // Proceed depending on current method
        if (jQuery.inArray(current_method, ['is_empty', 'is_not_empty', 'is_checked', 'is_not_checked']) !== -1) {
            element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type).find('select').parent().removeClass('rp_wcdpd_' + alias + '_setting_fields_single').addClass('rp_wcdpd_' + alias + '_setting_fields_double');
            text_field.parent().css('display', 'none');
            jQuery.rightpress.clear_field_value(text_field);
            text_field.prop('disabled', true);
        }
        else {
            element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type).find('select').parent().removeClass('rp_wcdpd_' + alias + '_setting_fields_double').addClass('rp_wcdpd_' + alias + '_setting_fields_single');
            element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type).find('.rp_wcdpd_' + key + '_' + alias + '_text').parent().css('display', 'block');
            text_field.prop('disabled', false);
        }
    }

    /**
     * Fix fields of coupons applied condition
     */
    function fix_coupons_applied_condition(key, alias, element, condition_type)
    {
        // Only proceed if condition type is coupons applied
        if (condition_type !== 'cart__coupons' && condition_type !== 'product_other__wc_coupons_applied') {
            return;
        }

        // Get current method
        var current_method = element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type + ' .rp_wcdpd_' + key + '_' + alias + '_method').val();

        // Reference coupons field
        var coupons_field = element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type + ' .rp_wcdpd_' + key + '_' + alias + '_coupons');

        // Proceed depending on current method
        if (jQuery.inArray(current_method, ['at_least_one_any', 'none_at_all']) !== -1) {
            element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type).find('select').parent().removeClass('rp_wcdpd_' + alias + '_setting_fields_single').addClass('rp_wcdpd_' + alias + '_setting_fields_triple');
            coupons_field.parent().css('display', 'none');
            jQuery.rightpress.clear_field_value(coupons_field);
            coupons_field.prop('disabled', true);
        }
        else {
            element.find('.rp_wcdpd_' + alias + '_setting_fields_' + condition_type).find('select').parent().removeClass('rp_wcdpd_' + alias + '_setting_fields_triple').addClass('rp_wcdpd_' + alias + '_setting_fields_single');
            coupons_field.parent().css('display', 'block');
            coupons_field.prop('disabled', false);
        }
    }

    /**
     * Fix product group element
     */
    function fix_group_product(key, element)
    {
        // Product condition type
        element.find('.rp_wcdpd_' + key + '_group_product_type').change(function() {
            toggle_group_product_items_field(key, element);
        });
        toggle_group_product_items_field(key, element);
    }

    /**
     * Toggle visibility of product group items fields
     */
    function toggle_group_product_items_field(key, element)
    {
        // Get current group_product type
        var current_type = element.find('.rp_wcdpd_' + key + '_group_product_type').val();

        // Show fields related to current type
        element.find('.rp_wcdpd_group_product_setting_fields_' + current_type).each(function() {
            jQuery(this).css('display', 'block');
            toggle_form_field_visibility(jQuery(this), true);
        });

        // Hide other currently displayed fields
        element.find('.rp_wcdpd_group_product_setting_fields').not('.rp_wcdpd_group_product_setting_fields_' + current_type).has('select:enabled').each(function() {
            jQuery(this).css('display', 'none');
            toggle_form_field_visibility(jQuery(this), false);
        });
    }

    /**
     * Initialize Select2 multiselect
     */
    function initialize_select2_multiselect(key, element, type)
    {
        // Field is not multiselect
        if (!jQuery.rightpress.field_is_multiselect(element)) {
            return;
        }

        var config = {
            width: '100%',
            minimumInputLength: 1,
            placeholder: rp_wcdpd.labels.select2_placeholder,
            escapeMarkup: function (text) {
                return text;
            },
            language: {
                noResults: function (params) {
                    return rp_wcdpd.labels.select2_no_results;
                }
            },
            ajax: {
                url:        rp_wcdpd.ajaxurl,
                type:       'POST',
                dataType:   'json',
                delay:      250,
                dataFilter: jQuery.rightpress.sanitize_json_response,
                data: function(params) {
                    return {
                        query:      params.term,
                        action:     'rp_wcdpd_load_multiselect_options',
                        type:       parse_multiselect_subject(key, element, type),
                        selected:   element.val()
                    };
                },
                processResults: function(data, page) {
                    return {
                        results: data.results
                    };
                }
            }
        };

        // Initialize Select2
        if (typeof RP_Select2 !== 'undefined') {
            RP_Select2.call(element, config);
        }
        // Initialize Select2
        else if (typeof element.selectWoo !== 'undefined') {
            element.selectWoo(config);
        }
    }

    /**
     * Parse multiselect field subject
     */
    function parse_multiselect_subject(key, element, type)
    {
        var subject = '';

        // Fix type for bogo products
        var type = (typeof type !== 'undefined' ? type : 'field_bogo');

        jQuery.each(element.attr('class').split(/\s+/), function(index, item) {
            if (item.indexOf('rp_wcdpd_' + key + '_' + type + '_') > -1) {
                subject = item.replace('rp_wcdpd_' + key + '_' + type + '_', '');
                return;
            }
        });

        return subject;
    }

    /**
     * Fix row header
     */
    function fix_row_header(element, key)
    {
        // Ensure element is row
        if (!element.hasClass('.rp_wcdpd_row')) {
            element = element.closest('.rp_wcdpd_row');
        }

        // Get title and note selectors
        var note_selector = '.rp_wcdpd_' + key + '_field_note';
        var title_selector = (key === 'product_pricing' ? note_selector : '.rp_wcdpd_' + key + '_field_title');

        // Title
        var title = element.find(title_selector).val();

        if (title !== 'undefined') {
            title = (title !== '' ? title : rp_wcdpd.labels.row_note_placeholder);
            element.find('.rp_wcdpd_row_title_title').html(title).css('display', 'inline-block');
        }

        // Note
        if (key !== 'product_pricing') {

            var note = element.find('.rp_wcdpd_' + key + '_field_note').val();

            if (note !== 'undefined') {
                element.find('.rp_wcdpd_row_title_note').html(note).css('display', (note === '' ? 'none' : 'inline-block'));
            }
        }

        // Display method
        if (key === 'product_pricing') {

            // Get method key
            var method_key = element.find('.rp_wcdpd_product_pricing_field_method').val();

            // Check if method is BOGO
            var is_bogo = ['bogo', 'bogo_xx'].indexOf(method_key) !== -1;
            var is_bogo_repeat = ['bogo_repeat', 'bogo_xx_repeat'].indexOf(method_key) !== -1;

            // Get BOGO quantities
            if (is_bogo || is_bogo_repeat) {
                var bogo_purchase = element.find('.rp_wcdpd_product_pricing_field_bogo_purchase_quantity').val();
                var bogo_receive = element.find('.rp_wcdpd_product_pricing_field_bogo_receive_quantity').val();
            }

            // Get method text
            if ((is_bogo || is_bogo_repeat) && bogo_purchase !== '' && bogo_receive !== '') {
                var property_key = 'title_format_bogo' + (is_bogo_repeat ? '_repeat' : '');
                var method_text = rp_wcdpd[property_key].replace('{{x}}', bogo_purchase).replace('{{y}}', bogo_receive);
            }
            else {
                var method_text = element.find('.rp_wcdpd_product_pricing_field_method option:selected').text();
            }

            // Display method text
            element.find('.rp_wcdpd_row_title_method').html(method_text).css('display', 'inline-block');
        }

        // Display pricing
        jQuery(['pricing', 'group_pricing', 'bogo_pricing']).each(function(index, selector) {
            if (element.find('.rp_wcdpd_' + key + '_field_' + selector + '_method:enabled').length) {

                // Get pricing settings
                var pricing_method = element.find('.rp_wcdpd_' + key + '_field_' + selector + '_method:enabled').val();
                var pricing_value = element.find('.rp_wcdpd_' + key + '_field_' + selector + '_value:enabled').val();

                // Both values are set
                if (typeof pricing_method !== 'undefined' && pricing_method !== '' && typeof pricing_value !== 'undefined' && pricing_value !== '' && jQuery.isNumeric(pricing_value)) {
                    var pricing_string = format_pricing_string(pricing_method, pricing_value);
                    element.find('.rp_wcdpd_row_title_pricing').html(pricing_string).css('display', 'inline-block');
                }
                // At least one value is not set
                else {
                    element.find('.rp_wcdpd_row_title_pricing').html('').css('display', 'none');
                }
            }
        });

        // Set up row state color coding
        set_up_row_state_color_coding(element);
    }

    /**
     * Format pricing string
     */
    function format_pricing_string(pricing_method, pricing_value)
    {
        var prepend = '';
        var append  = '';

        switch (pricing_method) {

            case 'discount__amount':
            case 'discount__percentage':
                prepend = '-';
                break;

            case 'fee__amount':
            case 'fee__percentage':
                prepend = '+';
                break;

            case 'discount__amount_per_product':
                prepend = '-';
                append  = ' ' + rp_wcdpd.labels.per_item;
                break;

            case 'discount_per_cart_item__amount':
            case 'discount_per_cart_item__percentage':
                prepend = '-';
                append  = ' ' + rp_wcdpd.labels.per_cart_item;
                break;

            case 'discount_per_cart_line__amount':
                prepend = '-';
                append  = ' ' + rp_wcdpd.labels.per_cart_line;
                break;

            case 'discount__amount_per_group':
                prepend = '-';
                append  = ' ' + rp_wcdpd.labels.per_group;
                break;

            case 'fixed__price_per_product':
                append  = ' ' + rp_wcdpd.labels.per_item;
                break;

            case 'fixed__price_per_group':
                append  = ' ' + rp_wcdpd.labels.per_group;
                break;

            case 'fee_per_cart_item__amount':
            case 'fee_per_cart_item__percentage':
                prepend = '+';
                append  = ' ' + rp_wcdpd.labels.per_cart_item;
                break;

            case 'fee_per_cart_line__amount':
                prepend = '+';
                append  = ' ' + rp_wcdpd.labels.per_cart_line;
                break;
        }

        pricing_value = Math.abs(pricing_value);

        // Format amount as percentage
        if (pricing_method.endsWith('__percentage')) {
            pricing_value += '%';
        }
        // Format amount
        else {

            // Ensure required amount of decimal places are displayed
            pricing_value = pricing_value.toFixed(Math.max(rp_wcdpd.price_decimals, (pricing_value.toString().split('.')[1] || []).length));

            // Format amount
            pricing_value = rp_wcdpd.price_format.replace('{{value}}', pricing_value);
        }

        // Join strings
        return prepend + pricing_value + append;
    }

    /**
     * Display correct product pricing settings area
     */
    function toggle_product_pricing_settings(element, key)
    {
        var current_method = element.val();
        var row = element.closest('.rp_wcdpd_row');

        // Display fields for current method
        row.find('.rp_wcdpd_if_' + current_method).each(function() {

            // Display container
            jQuery(this).css('display', 'block');

            // Only display/enable actual fields if current method is not group (group product fields are controlled by another method)
            if ((current_method !== 'group' && current_method !== 'group_repeat') || !jQuery(this).hasClass('rp_wcdpd_row_content_product_pricing_group_row')) {
                toggle_form_field_visibility(jQuery(this), true);
            }
        });

        // Hide other not currently hidden fields
        row.find('.rp_wcdpd_if').not('.rp_wcdpd_if_' + current_method).filter(function() { return jQuery(this).css('display') !== 'none'; }).each(function() {

            var to_hide = jQuery(this);

            // Delete product conditions if product conditions wrapper is selected
            jQuery.each(['bogo_product_condition', 'product_condition'], function(index, alias) {
                if (to_hide.hasClass('rp_wcdpd_row_content_' + alias + 's_row')) {
                    remove_child_wrapper(key, alias, row);
                    add_no(key, (alias + 's'), row);
                }
            });

            // Hide
            to_hide.css('display', 'none');

            // Toggle field visibility
            toggle_form_field_visibility(to_hide, false);
        });

        // Promotion type width
        row.find('.rp_wcdpd_product_pricing_field_method').closest('.rp_wcdpd_field').each(function() {

            jQuery(this).removeClass('rp_wcdpd_field_single rp_wcdpd_field_double');

            if (current_method === 'simple' || current_method === 'exclude' || current_method === 'restrict_purchase') {
                jQuery(this).addClass('rp_wcdpd_field_double');
            }
            else {
                jQuery(this).addClass('rp_wcdpd_field_single');
            }
        });

        // Fix group product inputs
        if (current_method === 'group' || current_method === 'group_repeat') {
            row.find('.rp_wcdpd_group_product').each(function() {
                toggle_group_product_items_field(key, jQuery(this));
            });
        }

        // Toggle volume rule pricing methods
        if (current_method === 'bulk' || current_method === 'tiered') {
            toggle_volume_pricing_methods(element, key);
        }

        // Clear quantity ranges
        row.find('.rp_wcdpd_quantity_range_wrapper').each(function() {
            if (current_method !== 'bulk' && current_method !== 'tiered') {
                remove_child_wrapper(key, 'quantity_range', row);
                add_no(key, 'quantity_ranges', row);
            }
        });

        // Clear group products
        row.find('.rp_wcdpd_group_product_wrapper').each(function() {
            if (current_method !== 'group' && current_method !== 'group_repeat') {
                remove_child_wrapper(key, 'group_product', row);
                add_no(key, 'group_products', row);
            }
        });

        // Hide exclusivity field for some pricing methods
        row.find('.rp_wcdpd_product_pricing_field_exclusivity').each(function() {
            var displayed = (current_method !== 'exclude' && current_method !== 'restrict_purchase');
            jQuery(this).css('display', (displayed ? 'block' : 'none'));
            jQuery(this).prop('disabled', !displayed);

            if (!displayed) {
                jQuery.rightpress.clear_field_value(jQuery(this));
            }
        });

        // Add empty quantity range
        if (current_method === 'bulk' || current_method === 'tiered') {
            if (!row.find('.rp_wcdpd_quantity_range_wrapper').length) {
                row.find('.rp_wcdpd_add_quantity_range button').click();
            }
        }

        // Add empty group product
        if (current_method === 'group' || current_method === 'group_repeat') {
            if (!row.find('.rp_wcdpd_group_product_wrapper').length) {
                row.find('.rp_wcdpd_add_group_product button').click();
            }
        }

        // Fix row header
        fix_row_header(row, key);
    }

    /**
     * Toggle volume pricing methods
     */
    function toggle_volume_pricing_methods(element, key)
    {
        var row = element.closest('.rp_wcdpd_row');
        var method = row.find('.rp_wcdpd_product_pricing_field_method').val();

        // Iterate over all pricing method fields in this rule (one for each quantity range)
        row.find('.rp_wcdpd_product_pricing_quantity_range_pricing_method').each(function() {

            // Get option to remove
            var current_option = jQuery(this).find('option[value="fixed__price_per_range"]');

            // Select another value if selected option is about to be removed
            if (jQuery(this).val() === 'fixed__price_per_range' && method === 'bulk') {
                jQuery(this).val('fixed__price');
            }

            // Add missing option
            if (method === 'tiered' && !current_option.length && jQuery(this).data('option_fixed__price_per_range_label')) {
                jQuery(this).find('optgroup').last().append('<option value="fixed__price_per_range">' + jQuery(this).data('option_fixed__price_per_range_label') + '</option>');
            }

            // Remove option if it exists
            if (method === 'bulk' && current_option.length) {
                jQuery(this).data('option_fixed__price_per_range_label', current_option.text());
                current_option.remove();
            }
        });
    }

    /**
     * Toggle cart item settings
     */
    function toggle_cart_item_settings(element, key)
    {
        var current_pricing_method = element.val();
        var row = element.closest('.rp_wcdpd_row');

        // Show product selection
        if (jQuery.inArray(current_pricing_method, ['discount_per_cart_item__amount', 'discount_per_cart_item__percentage', 'discount_per_cart_line__amount', 'fee_per_cart_item__amount', 'fee_per_cart_item__percentage', 'fee_per_cart_line__amount']) !== -1) {
            row.find('.rp_wcdpd_row_content_product_conditions_row').css('display', 'block');
        }
        // Hide product selection
        else {
            remove_child_wrapper(key, 'product_condition', row);
            add_no(key, 'product_conditions', row);
            row.find('.rp_wcdpd_row_content_product_conditions_row').css('display', 'none');
        }
    }

    /**
     * Toggle visibility of form fields contained by element
     */
    function toggle_form_field_visibility(element, displayed)
    {
        // Iterate over all form fields
        element.find('input, select').each(function() {

            // Enable/disable fields
            jQuery(this).prop('disabled', !displayed);

            // Clear field values
            if (!displayed) {
                jQuery.rightpress.clear_field_value(jQuery(this));
            }
        });
    }

    /**
     * Refresh accordion
     */
    function refresh_accordion(key)
    {
        jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper').accordion('refresh');
        jQuery('#rp_wcdpd_' + key + ' #rp_wcdpd_rule_wrapper').accordion('option', 'active', -1);
    }

    /**
     * Open requested rule on page load
     */
    if (typeof rp_wcdpd.open_rule_uid === 'string' && rp_wcdpd.open_rule_uid !== '') {

        // Reference wrapper
        var wrapper = jQuery('#rp_wcdpd_rule_wrapper');

        // Check if wrapper was selected
        if (wrapper.length) {

            // Find panel with this uid
            var row = wrapper.find('.rp_wcdpd_row input[value="' + rp_wcdpd.open_rule_uid + '"]').closest('.rp_wcdpd_row');

            // Check if row was selected
            if (row.length) {
                jQuery('#rp_wcdpd_rule_wrapper').accordion('option', 'active', row.index());
            }
        }
    }

    /**
     * Toggle discount/fee value input
     */
    jQuery('.rp_wcdpd_setting_total_limit').change(function() {
        var display = jQuery(this).val() !== '0';
        jQuery('.rp_wcdpd_setting_total_limit_value').prop('disabled', !display).css('display', (display ? 'inline-block' : 'none'));
    }).change();


































    /**
     * HELPER
     * Append template with values to selected element's content
     */
    function append(selector, template, values)
    {
        var html = get_template(template, values);

        if (typeof selector === 'object') {
            selector.append(html);
        }
        else {
            jQuery(selector).append(html);
        }
    }

    /**
     * HELPER
     * Prepend template with values to selected element's content
     */
    function prepend(selector, template, values)
    {
        var html = get_template(template, values);

        if (typeof selector === 'object') {
            selector.prepend(html);
        }
        else {
            jQuery(selector).prepend(html);
        }
    }

    /**
     * HELPER
     * Get template's html code
     */
    function get_template(template, values)
    {
        return populate_template(jQuery('#rp_wcdpd_' + template + '_template').html(), values);
    }

    /**
     * HELPER
     * Populate template with values
     */
    function populate_template(template, values)
    {
        for (var key in values) {
            if (values.hasOwnProperty(key)) {
                template = replace_macro(template, key, values[key]);
            }
        }

        return template;
    }

    /**
     * HELPER
     * Replace all instances of macro in string
     */
    function replace_macro(string, macro, value)
    {
        var macro = '{' + macro + '}';
        var regex = new RegExp(macro, 'g');
        return string.replace(regex, value);
    }






    /**
     * We are done by now, remove preloader
     */
    jQuery('#rp_wcdpd_preloader').remove();


});
