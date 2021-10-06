/**
 * Rules Interface Validation Scripts
 */

jQuery(document).ready(function() {

    'use strict';

    /**
     * Input validation methods
     *
     * Returns error message as string if validation fails
     * Returns null if validation succeeds
     */
    var input_validation_methods = {

        // Required
        required: function(input) {

            // Get value
            var value = input.val();

            // Set default to false
            var is_valid = false;

            // Value is set
            if (typeof value !== 'undefined') {

                // Value is array
                if (Array.isArray(value)) {

                    // Array must contain at least one element
                    is_valid = value.length > 0;
                }
                // Value is string
                else if (typeof value === 'string') {

                    // Value must not be empty string
                    is_valid = (value !== '');
                }
                // Other values - not really expecting anything here, are we?
                else {

                    // Value must cast to boolean true
                    is_valid = !!value;
                }
            }

            // Maybe return error message
            return is_valid ? null : rp_wcdpd.error_messages.required;
        },

        // Number - Min 0
        number_min_0: function(input) {
            return (!input.val() || +input.val() >= 0) ? null : rp_wcdpd.error_messages.number_min_0;
        },

        // Number - Natural
        number_natural: function(input) {
            return (!input.val() || +input.val() > 0) ? null : rp_wcdpd.error_messages.number_natural;
        },

        // Number - Min 1
        number_min_1: function(input) {
            return (!input.val() || +input.val() >= 1) ? null : rp_wcdpd.error_messages.number_min_1;
        },

        // Number - Whole
        number_whole: function(input) {
            return (!input.val() || (+input.val() % 1 === 0)) ? null : rp_wcdpd.error_messages.number_whole;
        }
    };

    /**
     * Disable default HTML5 validation
     */
    jQuery('form:has(.rp_wcdpd_rules)').attr('novalidate', 'novalidate');

    /**
     * Form submit handler
     */
    jQuery('form:has(.rp_wcdpd_rules)').on('submit', function(e) {

        var is_valid = true;

        var form = jQuery(this);

        // Validate total limit value
        form.find('.rp_wcdpd_setting_total_limit_value:enabled').first().each(function() {

            var element = jQuery(this);

            if (!validate_input(element)) {

                // Scroll to invalid input
                jQuery('html, body').animate({
                    scrollTop: 0
                }, 500).promise().then(function() {
                    display_error(element);
                    element.focus();
                });

                is_valid = false;
                return false;
            }
        });

        // Validate panels
        if (is_valid) {

            jQuery(this).find('.rp_wcdpd_row').each(function() {

                var panel = jQuery(this);

                // Validate panel
                if (!validate_panel(rp_wcdpd.current_tab, form, panel)) {

                    // Open invalid panel
                    form.find('#rp_wcdpd_rule_wrapper').accordion('option', 'active', panel.index());

                    // Scroll to invalid panel
                    jQuery('html, body').animate({
                        scrollTop: panel.find('.rp_wcdpd_accordion_handle').offset().top
                    }, 500).promise().then(function() {

                        // Get elements with errors
                        var elements_with_errors = panel.find(':data(rp-wcdpd-validation-error)');

                        // Display errors
                        elements_with_errors.each(function() {
                            display_error(jQuery(this));
                        });

                        // Focus first input
                        elements_with_errors.first().each(function() {
                            if (jQuery(this).is('input') || jQuery(this).is('select')) {
                                jQuery(this).focus();
                            }
                        });
                    });

                    is_valid = false;
                    return false;
                }
            });
        }

        // Do not submit form
        if (!is_valid) {
            e.preventDefault();
        }
    });

    /**
     * Validate single panel
     */
    function validate_panel(key, form, panel)
    {

        var is_valid = true;

        // Validate product pricing
        if (key === 'product_pricing') {
            if (!validate_product_pricing(key, form, panel)) {
                is_valid = false;
            }
        }

        // Iterate over non-disabled fields and validate them
        if (is_valid) {
            panel.find('input[data-rp-wcdpd-validation]:enabled, select[data-rp-wcdpd-validation]:enabled').each(function() {
                if (!validate_input(jQuery(this))) {
                    is_valid = false;
                    return false;
                }
            });
        }

        // Non-existent conditions present
        if (is_valid) {
            panel.find('.rp_wcdpd_condition_non_existent, .rp_wcdpd_condition_non_existent_taxonomy').each(function() {
                set_error(jQuery(this), rp_wcdpd.error_messages.condition_non_existent);
                is_valid = false;
                return false;
            });
        }

        // Disabled conditions present
        if (is_valid) {
            panel.find('.rp_wcdpd_condition_disabled, .rp_wcdpd_condition_disabled_taxonomy').each(function() {
                set_error(jQuery(this), rp_wcdpd.error_messages.condition_disabled);
                is_valid = false;
                return false;
            });
        }

        return is_valid;
    }

    /**
     * Validate product pricing
     */
    function validate_product_pricing(key, form, panel)
    {

        var is_valid = true;

        // Get product pricing method
        var method = panel.find('.rp_wcdpd_product_pricing_field_method').val();

        // Validate quantity ranges
        if (method === 'bulk' || method === 'tiered') {
            if (!validate_quantity_ranges(key, form, panel)) {
                is_valid = false;
            }
        }

        // Validate group products
        if (method === 'group' || method === 'group_repeat') {
            if (!validate_group_products(key, form, panel)) {
                is_valid = false;
            }
        }

        return is_valid;
    }

    /**
     * Validate quantity ranges
     */
    function validate_quantity_ranges(key, form, panel)
    {

        var is_valid = true;

        // Get quantity ranges
        var quantity_ranges = panel.find('.rp_wcdpd_quantity_range_wrapper .rp_wcdpd_quantity_range');
        var quantity_range_count = quantity_ranges.length;

        // No quantity ranges configured
        if (!quantity_range_count) {
            set_error(panel.find('.rp_wcdpd_row_content_quantity_ranges_row label'), rp_wcdpd.error_messages.no_quantity_ranges);
            is_valid = false;
        }

        // Check from and to quantities
        if (is_valid) {

            var last_from_field = false;
            var last_to_field = false;
            var last_from = false;
            var last_to = false;

            // Iterate over quantity ranges
            quantity_ranges.each(function() {

                // Reference quantity range
                var quantity_range = jQuery(this);

                // Get current from and to values
                var current_from_field = quantity_range.find('input.rp_wcdpd_product_pricing_quantity_range_from');
                var current_to_field = quantity_range.find('input.rp_wcdpd_product_pricing_quantity_range_to');
                var current_from = current_from_field.val();
                var current_to = current_to_field.val();

                // Range not yet filled in - input validation will take care of it
                if (current_from === '') {
                    return false;
                }

                // From is higher than to
                if (current_to !== '' && current_from > +current_to) {
                    set_error(current_to_field, rp_wcdpd.error_messages.quantity_ranges_from_more_than_to);
                    is_valid = false;
                    return false;
                }

                // Current range is not first
                if (last_from !== false) {

                    // Last range had no "To" value - we shouldn't have subsequent ranges
                    if (last_to === '') {
                        set_error(last_to_field, rp_wcdpd.error_messages.quantity_ranges_last_to_open);
                        is_valid = false;
                        return false;
                    }

                    // Current from must be higher than previous from
                    if (+current_from <= +last_from) {
                        set_error(current_from_field, rp_wcdpd.error_messages.quantity_ranges_last_from_higher);
                        is_valid = false;
                        return false;
                    }

                    // Ranges overlap
                    if (+current_from <= +last_to) {
                        set_error(current_from_field, rp_wcdpd.error_messages.quantity_ranges_overlap);
                        is_valid = false;
                        return false;
                    }

                    // Ranges overlap
                    if (current_to !== '' && +current_to <= +last_to) {
                        set_error(current_to_field, rp_wcdpd.error_messages.quantity_ranges_overlap);
                        is_valid = false;
                        return false;
                    }
                }

                last_from_field = current_from_field;
                last_to_field = current_to_field;
                last_from = current_from;
                last_to = current_to;
            });
        }

        return is_valid;
    }

    /**
     * Validate group products
     */
    function validate_group_products(key, form, panel)
    {

        var is_valid = true;

        // Get group products
        var group_products = panel.find('.rp_wcdpd_group_product_wrapper .rp_wcdpd_group_product');
        var group_product_count = group_products.length;

        // No group products configured
        if (!group_product_count) {
            set_error(panel.find('.rp_wcdpd_row_content_product_pricing_group_row label'), rp_wcdpd.error_messages.no_group_products);
            is_valid = false;
        }

        return is_valid;
    }

    /**
     * Validate single input
     */
    function validate_input(input)
    {

        var is_valid = true;

        // Check if validation rules are present
        if (typeof input.data('rp-wcdpd-validation') !== 'undefined') {

            // Get input validation rules
            var validation_rules = input.data('rp-wcdpd-validation').split(',');

            // Check each validation rule
            jQuery.each(validation_rules, function(index, validation_rule) {

                // Validate input
                var error_message = input_validation_methods[validation_rule](input);

                // Check if error message was returned which indicates validation failure
                if (error_message !== null) {

                    // Get element to set error on
                    var error_element = typeof input.data('select2') !== 'undefined' ? input.next('.select2') : input;

                    // Set error and return false
                    set_error(error_element, error_message);
                    is_valid = false;
                    return false;
                }
            });
        }

        return is_valid;
    }

    /**
     * Set element state to error
     */
    function set_error(element, message)
    {

        // Get message
        if (typeof message === 'undefined' || message === null) {
            message = rp_wcdpd.error_messages.generic_error;
        }

        // Set error
        element.data('rp-wcdpd-validation-error', message);
    }

    /**
     * Display error
     */
    function display_error(element)
    {

        // Get message
        var message = element.data('rp-wcdpd-validation-error');

        // Set tooltip
        element.on('mouseleave', function (event) {
            event.stopImmediatePropagation();
        }).tooltip({
            content: message,
            items: ':data(rp-wcdpd-validation-error)',
            tooltipClass: 'rp_wcdpd_validation_error',
            classes: {
                'ui-tooltip': 'rp_wcdpd_validation_error'
            },
            position: {
                my: 'center top',
                at: 'left+110 bottom+10'
            },
            create: function() {

                // Adjust position for multiselect fields
                if (element.is('select[multiple]')) {
                    element.tooltip('option', 'position', {
                        my: 'center top',
                        at: 'left+110 bottom+51'
                    });
                }

                // Remove tooltip on interaction
                var removal_selectors = element.add('html, body');
                removal_selectors.on('click keyup change', {element: element, removal_selectors: removal_selectors}, remove_tooltip);
            }
        }).tooltip('open');
    }

    /**
     * Remove tooltip
     */
    function remove_tooltip(event)
    {

        // Get args
        var element = event.data.element;
        var removal_selectors = event.data.removal_selectors;

        // Destroy tooltip
        if (element.data('ui-tooltip')) {
            element.tooltip('destroy');
        }

        // Remove error message
        element.removeData('rp-wcdpd-validation-error');

        // Remove event listeners
        removal_selectors.off('click keyup change', remove_tooltip);
    }





});
