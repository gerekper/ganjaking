<?php

/**
 * View for advanced options templates
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<!-- WOOCOMMERCE DYNAMIC PRICING & DISCOUNTS TEMPLATES -->
<div id="rp_wcdpd_templates" style="display: none;">

    <!-- NO RULES CONFIGURED -->
    <div id="rp_wcdpd_no_rows_template">
        <div id="rp_wcdpd_no_rows"><?php esc_html_e('No rules configured.', 'rp_wcdpd'); ?></div>
    </div>

    <!-- ADD RULE BUTTON -->
    <div id="rp_wcdpd_add_row_template">
        <div id="rp_wcdpd_add_row">
            <button type="button" class="button" value="<?php esc_html_e('Add Rule', 'rp_wcdpd'); ?>">
                <?php esc_html_e('Add Rule', 'rp_wcdpd'); ?>
            </button>
        </div>
    </div>

    <!-- RULE WRAPPER -->
    <div id="rp_wcdpd_rule_wrapper_template">
        <div id="rp_wcdpd_rule_wrapper"></div>
    </div>

    <!-- ROW -->
    <div id="rp_wcdpd_row_template">

        <div class="rp_wcdpd_row">

            <div class="rp_wcdpd_accordion_handle">
                <div class="rp_wcdpd_row_sort_handle"><span class="dashicons dashicons-menu"></span></div>
                <span class="rp_wcdpd_row_title">
                    <span class="rp_wcdpd_row_title_title" style="display: none;"></span>
                    <span class="rp_wcdpd_row_title_note" style="display: none;"></span>
                    <span class="rp_wcdpd_row_title_method" style="display: none;"></span>
                    <span class="rp_wcdpd_row_title_pricing" style="display: none;"></span>
                </span>
                <div class="rp_wcdpd_row_remove_handle"><span class="dashicons dashicons-no-alt"></span></div>
                <div class="rp_wcdpd_row_duplicate_handle"><span class="dashicons dashicons-admin-page"></span></div>

                <?php RightPress_Forms::grouped_select(array(
                    'id'                        => 'rp_wcdpd_' . $current_tab . '_exclusivity_{i}',
                    'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][exclusivity]',
                    'class'                     => 'rp_wcdpd_' . $current_tab . '_field_exclusivity rp_wcdpd_field_exclusivity',
                    'options'                   => RP_WCDPD_Settings::get_exclusivity_methods_for_display($current_tab),
                    'data-rp-wcdpd-validation'  => 'required',
                ), false); ?>

                <?php RightPress_Forms::hidden(array(
                    'id'        => 'rp_wcdpd_' . $current_tab . '_uid_{i}',
                    'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][uid]',
                ), false); ?>

            </div>

            <div class="rp_wcdpd_row_content">

                <div class="rp_wcdpd_loading_data"><?php esc_html_e('Loading data...', 'rp_wcdpd'); ?></div>

                <?php RightPress_Forms::hidden(array(
                    'id'        => 'rp_wcdpd_' . $current_tab . '_unchanged_{i}',
                    'class'     => 'rp_wcdpd_' . $current_tab . '_unchanged',
                    'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][unchanged]',
                    'value'     => '1',
                ), false); ?>

            </div>
        </div>
    </div>

    <!-- ROW CONTENT -->
    <div id="rp_wcdpd_row_content_template">

        <div class="rp_wcdpd_row_content_first_row">

            <?php if ($current_tab === 'product_pricing'): ?>
                <div class="rp_wcdpd_field rp_wcdpd_field_double rp_wcdpd_no_left_margin">
                    <?php RightPress_Forms::grouped_select(array(
                        'id'                        => 'rp_wcdpd_' . $current_tab . '_method_{i}',
                        'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][method]',
                        'class'                     => 'rp_wcdpd_' . $current_tab . '_field_method',
                        'options'                   => RP_WCDPD_Settings::get_product_pricing_methods_for_display(),
                        'label'                     => esc_html__('Method', 'rp_wcdpd'),
                        'data-rp-wcdpd-validation'  => 'required',
                    ), false); ?>
                </div>
            <?php endif; ?>

            <?php if (in_array($current_tab, array('cart_discounts', 'checkout_fees'), true)): ?>
                <div class="rp_wcdpd_field rp_wcdpd_field_double rp_wcdpd_no_left_margin">
                    <?php RightPress_Forms::text(array(
                        'id'                        => 'rp_wcdpd_' . $current_tab . '_title_{i}',
                        'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][title]',
                        'class'                     => 'rp_wcdpd_' . $current_tab . '_field_title',
                        'label'                     => esc_html__('Title', 'rp_wcdpd') . ' <span class="rp_wcdpd_settings_label_extra">- ' . esc_html__('Public', 'rp_wcdpd') . '</span>',
                        'data-rp-wcdpd-validation'  => 'required',
                    )); ?>
                </div>
            <?php endif; ?>

            <?php if ($current_tab === 'product_pricing'): ?>
                <div class="rp_wcdpd_field rp_wcdpd_field_single rp_wcdpd_if rp_wcdpd_if_bulk rp_wcdpd_if_tiered rp_wcdpd_if_group rp_wcdpd_if_group_repeat rp_wcdpd_if_bogo rp_wcdpd_if_bogo_repeat rp_wcdpd_if_bogo_xx rp_wcdpd_if_bogo_xx_repeat">
                    <?php RightPress_Forms::grouped_select(array(
                        'id'                        => 'rp_wcdpd_' . $current_tab . '_quantities_based_on_{i}',
                        'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][quantities_based_on]',
                        'class'                     => 'rp_wcdpd_' . $current_tab . '_field_quantities_based_on',
                        'options'                   => RP_WCDPD_Settings::get_quantities_based_on_methods_for_display(),
                        'label'                     => esc_html__('Quantities', 'rp_wcdpd'),
                        'data-rp-wcdpd-validation'  => 'required',
                    ), true); ?>
                </div>
            <?php endif; ?>

            <div class="rp_wcdpd_field rp_wcdpd_field_double">
                <?php RightPress_Forms::text(array(
                    'id'        => 'rp_wcdpd_' . $current_tab . '_note_{i}',
                    'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][note]',
                    'class'     => 'rp_wcdpd_' . $current_tab . '_field_note',
                    'label'     => esc_html__('Note', 'rp_wcdpd') . ' <span class="rp_wcdpd_settings_label_extra">- ' . esc_html__('Private', 'rp_wcdpd') . '</span>',
                )); ?>
            </div>

            <div class="rp_wcdpd_clear_both"></div>

        </div>

        <div class="rp_wcdpd_row_content_public_description rp_wcdpd_if rp_wcdpd_if_simple rp_wcdpd_if_bulk rp_wcdpd_if_tiered rp_wcdpd_if_group rp_wcdpd_if_group_repeat rp_wcdpd_if_bogo rp_wcdpd_if_bogo_repeat rp_wcdpd_if_bogo_xx rp_wcdpd_if_bogo_xx_repeat">

            <div class="rp_wcdpd_field rp_wcdpd_field_full">
                <?php RightPress_Forms::text(array(
                    'id'        => 'rp_wcdpd_' . $current_tab . '_public_note_{i}',
                    'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][public_note]',
                    'class'     => 'rp_wcdpd_' . $current_tab . '_field_public_note',
                    'label'     => esc_html__('Description', 'rp_wcdpd') . ' <span class="rp_wcdpd_settings_label_extra">- ' . esc_html__('Public', 'rp_wcdpd') . '</span>',
                )); ?>
            </div>

            <div class="rp_wcdpd_clear_both"></div>

        </div>

        <?php if ($current_tab === 'product_pricing'): ?>

            <div class="rp_wcdpd_row_content_product_pricing_row rp_wcdpd_row_content_product_pricing_bogo_row rp_wcdpd_if rp_wcdpd_if_bogo rp_wcdpd_if_bogo_repeat rp_wcdpd_if_bogo_xx rp_wcdpd_if_bogo_xx_repeat" style="display: none;">
                <div class="rp_wcdpd_field rp_wcdpd_field_full">
                    <label><?php esc_html_e('Quantities & Discount', 'rp_wcdpd'); ?></label>
                    <div class="rp_wcdpd_inner_wrapper">

                        <div class="rp_wcdpd_field rp_wcdpd_field_single rp_wcdpd_no_left_margin">
                            <?php RightPress_Forms::number(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_bogo_purchase_quantity_{i}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_purchase_quantity]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_field_bogo_purchase_quantity',
                                'placeholder'               => esc_html__('Quantity', 'rp_wcdpd'),
                                'label'                     => esc_html__('Buy', 'rp_wcdpd') . ' <span class="rp_wcdpd_settings_label_extra">- ' . esc_html__('At Full Price', 'rp_wcdpd') . '</span>',
                                'disabled'                  => 'disabled',
                                'data-rp-wcdpd-validation'  => 'required,number_min_1,number_whole',
                            )); ?>
                        </div>
                        <div class="rp_wcdpd_field rp_wcdpd_field_single">
                            <?php RightPress_Forms::number(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_bogo_receive_quantity_{i}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_receive_quantity]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_field_bogo_receive_quantity',
                                'placeholder'               => esc_html__('Quantity', 'rp_wcdpd'),
                                'label'                     => esc_html__('Get', 'rp_wcdpd') . ' <span class="rp_wcdpd_settings_label_extra">- ' . esc_html__('Discounted', 'rp_wcdpd') . '</span>',
                                'disabled'                  => 'disabled',
                                'data-rp-wcdpd-validation'  => 'required,number_min_1,number_whole',
                            )); ?>
                        </div>

                        <div class="rp_wcdpd_field rp_wcdpd_field_single">
                            <?php RightPress_Forms::grouped_select(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_bogo_pricing_method_{i}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_pricing_method]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_field_bogo_pricing_method',
                                'options'                   => RP_WCDPD_Pricing::get_pricing_methods_for_display('product_pricing_bogo'),
                                'label'                     => esc_html__('Discount', 'rp_wcdpd'),
                                'disabled'                  => 'disabled',
                                'data-rp-wcdpd-validation'  => 'required',
                            ), true); ?>
                        </div>
                        <div class="rp_wcdpd_field rp_wcdpd_field_single">
                            <?php RightPress_Forms::decimal(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_bogo_pricing_value_{i}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_pricing_value]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_field_bogo_pricing_value',
                                'placeholder'               => '0.00',
                                'label'                     => '&nbsp;',
                                'disabled'                  => 'disabled',
                                'data-rp-wcdpd-validation'  => 'required,number_min_0',
                            )); ?>
                        </div>

                        <div class="rp_wcdpd_clear_both"></div>
                    </div>
                </div>
            </div>

            <div class="rp_wcdpd_row_content_product_pricing_row rp_wcdpd_row_content_child_row rp_wcdpd_row_content_quantity_ranges_row rp_wcdpd_if rp_wcdpd_if_bulk rp_wcdpd_if_tiered" style="display: none;">
                <div class="rp_wcdpd_field rp_wcdpd_field_full">
                    <label><?php esc_html_e('Quantity Ranges', 'rp_wcdpd'); ?></label>
                    <div class="rp_wcdpd_inner_wrapper">
                        <div class="rp_wcdpd_add_quantity_range rp_wcdpd_add_child_element">
                            <button type="button" class="button" value="<?php esc_html_e('Add Range', 'rp_wcdpd'); ?>">
                                <?php esc_html_e('Add Range', 'rp_wcdpd'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <div class="rp_wcdpd_row_content_product_pricing_row rp_wcdpd_row_content_pricing_row <?php echo ($current_tab === 'product_pricing' ? 'rp_wcdpd_if rp_wcdpd_if_simple' : ''); ?>">
            <div class="rp_wcdpd_field rp_wcdpd_field_full">
                <label><?php echo RP_WCDPD_Pricing::get_pricing_settings_label($current_tab); ?></label>
                <div class="rp_wcdpd_inner_wrapper">

                    <div class="rp_wcdpd_field rp_wcdpd_field_double rp_wcdpd_no_left_margin">
                        <?php RightPress_Forms::grouped_select(array(
                            'id'                        => 'rp_wcdpd_' . $current_tab . '_pricing_method_{i}',
                            'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][pricing_method]',
                            'class'                     => 'rp_wcdpd_' . $current_tab . '_field_pricing_method',
                            'options'                   => RP_WCDPD_Pricing::get_pricing_methods_for_display($current_tab . '_simple'),
                            'data-rp-wcdpd-validation'  => 'required',
                        ), true); ?>
                    </div>
                    <div class="rp_wcdpd_field rp_wcdpd_field_double">
                        <?php RightPress_Forms::decimal(array(
                            'id'                        => 'rp_wcdpd_' . $current_tab . '_pricing_value_{i}',
                            'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][pricing_value]',
                            'class'                     => 'rp_wcdpd_' . $current_tab . '_field_pricing_value',
                            'placeholder'               => '0.00',
                            'data-rp-wcdpd-validation'  => 'required,number_min_0',
                        )); ?>
                    </div>

                    <div class="rp_wcdpd_clear_both"></div>

                </div>
                <div class="rp_wcdpd_clear_both"></div>
            </div>
            <div class="rp_wcdpd_clear_both"></div>
        </div>

        <?php if ($current_tab === 'product_pricing'): ?>

            <div class="rp_wcdpd_row_content_product_pricing_row rp_wcdpd_row_content_pricing_row <?php echo ($current_tab === 'product_pricing' ? 'rp_wcdpd_if rp_wcdpd_if_group rp_wcdpd_if_group_repeat' : ''); ?>">
                <div class="rp_wcdpd_field rp_wcdpd_field_full">
                    <label><?php echo RP_WCDPD_Pricing::get_pricing_settings_label($current_tab); ?></label>
                    <div class="rp_wcdpd_inner_wrapper">

                        <div class="rp_wcdpd_field rp_wcdpd_field_double rp_wcdpd_no_left_margin">
                            <?php RightPress_Forms::grouped_select(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_group_pricing_method_{i}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][group_pricing_method]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_field_group_pricing_method',
                                'options'                   => RP_WCDPD_Pricing::get_pricing_methods_for_display('product_pricing_group'),
                                'data-rp-wcdpd-validation'  => 'required',
                            ), true); ?>
                        </div>
                        <div class="rp_wcdpd_field rp_wcdpd_field_double">
                            <?php RightPress_Forms::decimal(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_group_pricing_value_{i}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][group_pricing_value]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_field_group_pricing_value',
                                'placeholder'               => '0.00',
                                'data-rp-wcdpd-validation'  => 'required,number_min_0',
                            )); ?>
                        </div>

                        <div class="rp_wcdpd_clear_both"></div>

                    </div>
                </div>
            </div>

            <div class="rp_wcdpd_row_content_product_pricing_row rp_wcdpd_row_content_product_pricing_group_row rp_wcdpd_row_content_child_row rp_wcdpd_row_content_group_products_row rp_wcdpd_if rp_wcdpd_if_group rp_wcdpd_if_group_repeat" style="display: none;">
                <div class="rp_wcdpd_field rp_wcdpd_field_full">
                    <label><?php esc_html_e('Product Group', 'rp_wcdpd'); ?></label>
                    <div class="rp_wcdpd_inner_wrapper">
                        <div class="rp_wcdpd_add_group_product rp_wcdpd_add_child_element">
                            <button type="button" class="button" value="<?php esc_html_e('Add Product', 'rp_wcdpd'); ?>">
                                <?php esc_html_e('Add Product', 'rp_wcdpd'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <div class="rp_wcdpd_row_content_child_row rp_wcdpd_row_content_product_conditions_row rp_wcdpd_if rp_wcdpd_if_simple rp_wcdpd_if_bulk rp_wcdpd_if_tiered rp_wcdpd_if_bogo rp_wcdpd_if_bogo_repeat rp_wcdpd_if_bogo_xx rp_wcdpd_if_bogo_xx_repeat rp_wcdpd_if_exclude rp_wcdpd_if_restrict_purchase" style="display: none;">
            <div class="rp_wcdpd_field rp_wcdpd_field_full">
                <?php if ($current_tab !== 'product_pricing'): ?>
                    <label><?php esc_html_e('Items', 'rp_wcdpd'); ?></label>
                <?php else: ?>
                    <label class="rp_wcdpd_if rp_wcdpd_if_simple rp_wcdpd_if_bulk rp_wcdpd_if_tiered rp_wcdpd_if_bogo_xx rp_wcdpd_if_bogo_xx_repeat rp_wcdpd_if_exclude rp_wcdpd_if_restrict_purchase" style="display: none;"><?php esc_html_e('Products', 'rp_wcdpd'); ?></label>
                    <label class="rp_wcdpd_if rp_wcdpd_if_bogo rp_wcdpd_if_bogo_repeat" style="display: none;"><?php esc_html_e('Products - Buy', 'rp_wcdpd'); ?></label>
                <?php endif; ?>

                <div class="rp_wcdpd_inner_wrapper">
                    <div class="rp_wcdpd_add_product_condition rp_wcdpd_add_child_element">
                        <button type="button" class="button" value="<?php esc_html_e('Add Product', 'rp_wcdpd'); ?>">
                            <?php esc_html_e('Add Product', 'rp_wcdpd'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="rp_wcdpd_clear_both"></div>
        </div>

        <?php if ($current_tab === 'product_pricing'): ?>
            <div class="rp_wcdpd_row_content_child_row rp_wcdpd_row_content_bogo_product_conditions_row rp_wcdpd_if   rp_wcdpd_if_bogo rp_wcdpd_if_bogo_repeat" style="display: none;">
                <div class="rp_wcdpd_field rp_wcdpd_field_full">
                    <label><?php esc_html_e('Products - Get', 'rp_wcdpd'); ?></label>
                    <div class="rp_wcdpd_inner_wrapper">
                        <div class="rp_wcdpd_add_bogo_product_condition rp_wcdpd_add_child_element">
                            <button type="button" class="button" value="<?php esc_html_e('Add Product', 'rp_wcdpd'); ?>">
                                <?php esc_html_e('Add Product', 'rp_wcdpd'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="rp_wcdpd_clear_both"></div>
            </div>
        <?php endif; ?>

        <div class="rp_wcdpd_row_content_child_row rp_wcdpd_row_content_conditions_row">
            <div class="rp_wcdpd_field rp_wcdpd_field_full">
                <label><?php esc_html_e('Conditions', 'rp_wcdpd'); ?></label>
                <div class="rp_wcdpd_inner_wrapper">
                    <div class="rp_wcdpd_add_condition rp_wcdpd_add_child_element">
                        <button type="button" class="button" value="<?php esc_html_e('Add Condition', 'rp_wcdpd'); ?>">
                            <?php esc_html_e('Add Condition', 'rp_wcdpd'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="rp_wcdpd_clear_both"></div>
        </div>
    </div>

    <!-- NO PRODUCT CONDITIONS -->
    <div id="rp_wcdpd_no_product_conditions_template">
        <div class="rp_wcdpd_no_product_conditions rp_wcdpd_no_child_elements">
            <?php if ($current_tab === 'product_pricing'): ?>
                <?php esc_html_e('Applies to all products.', 'rp_wcdpd') ?>
            <?php else: ?>
                <?php esc_html_e('Applies to all items.', 'rp_wcdpd') ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- PRODUCT CONDITIONS WRAPPER -->
    <div id="rp_wcdpd_product_condition_wrapper_template">
        <div class="rp_wcdpd_product_condition_wrapper"></div>
    </div>

    <!-- PRODUCT CONDITION -->
    <div id="rp_wcdpd_product_condition_template">
        <div class="rp_wcdpd_product_condition rp_wcdpd_child_element">
            <div class="rp_wcdpd_product_condition_sort rp_wcdpd_child_element_sort">
                <div class="rp_wcdpd_product_condition_sort_handle rp_wcdpd_child_element_sort_handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
            </div>

            <div class="rp_wcdpd_product_condition_content rp_wcdpd_child_element_content">

                <div class="rp_wcdpd_product_condition_setting rp_wcdpd_product_condition_setting_single rp_wcdpd_product_condition_setting_type">
                    <?php RightPress_Forms::grouped_select(array(
                        'id'                        => 'rp_wcdpd_' . $current_tab . '_product_conditions_{i}_type_{j}',
                        'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][product_conditions][{j}][type]',
                        'class'                     => 'rp_wcdpd_' . $current_tab . '_product_condition_type rp_wcdpd_child_element_field rightpress_select2 rp_wcdpd_select2 rp_wcdpd_select2_grouped',
                        'options'                   => RP_WCDPD_Controller_Conditions::get_items_for_display($current_tab . '_product'),
                        'data-rp-wcdpd-validation'  => 'required',
                    ), true); ?>
                </div>

                <div class="rp_wcdpd_product_condition_setting_fields_wrapper"></div>

                <?php RightPress_Forms::hidden(array(
                    'id'        => 'rp_wcdpd_' . $current_tab . '_product_conditions_{i}_uid_{j}',
                    'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][product_conditions][{j}][uid]',
                ), false); ?>

                <div class="rp_wcdpd_clear_both"></div>
            </div>

            <div class="rp_wcdpd_product_condition_remove rp_wcdpd_child_element_remove">
                <div class="rp_wcdpd_product_condition_remove_handle rp_wcdpd_child_element_remove_handle">
                    <span class="dashicons dashicons-no-alt"></span>
                </div>
            </div>
            <div class="rp_wcdpd_clear_both"></div>
        </div>
    </div>

    <!-- PRODUCT CONDITION FIELDS -->
    <?php foreach(RP_WCDPD_Controller_Conditions::get_items_for_display($current_tab . '_product') as $group_key => $group): ?>
        <?php foreach($group['options'] as $option_key => $option): ?>

            <?php $combined_key = $group_key . '__' . $option_key; ?>

            <div id="rp_wcdpd_product_condition_setting_fields_<?php echo $combined_key ?>_template">
                <div class="rp_wcdpd_product_condition_setting_fields rp_wcdpd_product_condition_setting_fields_<?php echo $combined_key ?>">

                    <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'before', 'product_condition'); ?>

                    <div class="rp_wcdpd_product_condition_setting_fields_<?php echo (in_array($combined_key, array('product_property__on_sale', 'product_other__pricing_rules_applied'), true) ? 'triple' : 'single'); ?>">
                        <?php RightPress_Forms::select(array(
                            'id'                        => 'rp_wcdpd_' . $current_tab . '_product_conditions_{i}_method_option_{j}',
                            'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][product_conditions][{j}][method_option]',
                            'class'                     => 'rp_wcdpd_' . $current_tab . '_product_condition_method rp_wcdpd_child_element_field',
                            'options'                   => RP_WCDPD_Controller_Conditions::get_condition_method_options_for_display($combined_key),
                            'data-rp-wcdpd-validation'  => 'required',
                        )); ?>
                    </div>

                    <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'after', 'product_condition'); ?>

                    <div class="rp_wcdpd_clear_both"></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php if ($current_tab === 'product_pricing'): ?>

        <!-- NO BOGO PRODUCT CONDITIONS -->
        <div id="rp_wcdpd_no_bogo_product_conditions_template">
            <div class="rp_wcdpd_no_bogo_product_conditions rp_wcdpd_no_child_elements">
                <?php esc_html_e('Applies to all products.', 'rp_wcdpd') ?>
            </div>
        </div>

        <!-- BOGO PRODUCT CONDITIONS WRAPPER -->
        <div id="rp_wcdpd_bogo_product_condition_wrapper_template">
            <div class="rp_wcdpd_bogo_product_condition_wrapper"></div>
        </div>

        <!-- BOGO PRODUCT CONDITION -->
        <div id="rp_wcdpd_bogo_product_condition_template">
            <div class="rp_wcdpd_bogo_product_condition rp_wcdpd_child_element">
                <div class="rp_wcdpd_bogo_product_condition_sort rp_wcdpd_child_element_sort">
                    <div class="rp_wcdpd_bogo_product_condition_sort_handle rp_wcdpd_child_element_sort_handle">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                </div>

                <div class="rp_wcdpd_bogo_product_condition_content rp_wcdpd_child_element_content">

                    <div class="rp_wcdpd_bogo_product_condition_setting rp_wcdpd_bogo_product_condition_setting_single rp_wcdpd_bogo_product_condition_setting_type">
                        <?php RightPress_Forms::grouped_select(array(
                            'id'                        => 'rp_wcdpd_' . $current_tab . '_bogo_product_conditions_{i}_type_{j}',
                            'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_product_conditions][{j}][type]',
                            'class'                     => 'rp_wcdpd_' . $current_tab . '_bogo_product_condition_type rp_wcdpd_child_element_field rightpress_select2 rp_wcdpd_select2 rp_wcdpd_select2_grouped',
                            'options'                   => RP_WCDPD_Controller_Conditions::get_items_for_display($current_tab . '_product'),
                            'data-rp-wcdpd-validation'  => 'required',
                        ), true); ?>
                    </div>

                    <div class="rp_wcdpd_bogo_product_condition_setting_fields_wrapper"></div>

                    <?php RightPress_Forms::hidden(array(
                        'id'        => 'rp_wcdpd_' . $current_tab . '_bogo_product_conditions_{i}_uid_{j}',
                        'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_product_conditions][{j}][uid]',
                    ), false); ?>

                    <div class="rp_wcdpd_clear_both"></div>
                </div>

                <div class="rp_wcdpd_bogo_product_condition_remove rp_wcdpd_child_element_remove">
                    <div class="rp_wcdpd_bogo_product_condition_remove_handle rp_wcdpd_child_element_remove_handle">
                        <span class="dashicons dashicons-no-alt"></span>
                    </div>
                </div>
                <div class="rp_wcdpd_clear_both"></div>
            </div>
        </div>

        <!-- BOGO PRODUCT CONDITION FIELDS -->
        <?php foreach(RP_WCDPD_Controller_Conditions::get_items_for_display($current_tab . '_bogo_product') as $group_key => $group): ?>
            <?php foreach($group['options'] as $option_key => $option): ?>

                <?php $combined_key = $group_key . '__' . $option_key; ?>

                <div id="rp_wcdpd_bogo_product_condition_setting_fields_<?php echo $combined_key ?>_template">
                    <div class="rp_wcdpd_bogo_product_condition_setting_fields rp_wcdpd_bogo_product_condition_setting_fields_<?php echo $combined_key ?>">

                        <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'before', 'bogo_product_condition'); ?>

                        <div class="rp_wcdpd_bogo_product_condition_setting_fields_<?php echo (in_array($combined_key, array('product_property__on_sale'), true) ? 'triple' : 'single'); ?>">
                            <?php RightPress_Forms::select(array(
                                'id'                        => 'rp_wcdpd_' . $current_tab . '_bogo_product_conditions_{i}_method_option_{j}',
                                'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][bogo_product_conditions][{j}][method_option]',
                                'class'                     => 'rp_wcdpd_' . $current_tab . '_bogo_product_condition_method rp_wcdpd_child_element_field',
                                'options'                   => RP_WCDPD_Controller_Conditions::get_condition_method_options_for_display($combined_key),
                                'data-rp-wcdpd-validation'  => 'required',
                            )); ?>
                        </div>

                        <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'after', 'bogo_product_condition'); ?>

                        <div class="rp_wcdpd_clear_both"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>

    <?php endif; ?>

    <!-- NO CONDITIONS -->
    <div id="rp_wcdpd_no_conditions_template">
        <div class="rp_wcdpd_no_conditions rp_wcdpd_no_child_elements"><?php esc_html_e('Applies in all cases.', 'rp_wcdpd'); ?></div>
    </div>

    <!-- CONDITIONS WRAPPER -->
    <div id="rp_wcdpd_condition_wrapper_template">
        <div class="rp_wcdpd_condition_wrapper"></div>
    </div>

    <!-- CONDITION -->
    <div id="rp_wcdpd_condition_template">
        <div class="rp_wcdpd_condition rp_wcdpd_child_element">
            <div class="rp_wcdpd_condition_sort rp_wcdpd_child_element_sort">
                <div class="rp_wcdpd_condition_sort_handle rp_wcdpd_child_element_sort_handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
            </div>

            <div class="rp_wcdpd_condition_content rp_wcdpd_child_element_content">

                <div class="rp_wcdpd_condition_setting rp_wcdpd_condition_setting_single rp_wcdpd_condition_setting_type">
                    <?php RightPress_Forms::grouped_select(array(
                        'id'                        => 'rp_wcdpd_' . $current_tab . '_conditions_{i}_type_{j}',
                        'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][conditions][{j}][type]',
                        'class'                     => 'rp_wcdpd_' . $current_tab . '_condition_type rp_wcdpd_child_element_field rightpress_select2 rp_wcdpd_select2 rp_wcdpd_select2_grouped',
                        'options'                   => RP_WCDPD_Controller_Conditions::get_items_for_display($current_tab),
                        'data-rp-wcdpd-validation'  => 'required',
                    ), true); ?>
                </div>

                <div class="rp_wcdpd_condition_setting_fields_wrapper"></div>

                <?php RightPress_Forms::hidden(array(
                    'id'        => 'rp_wcdpd_' . $current_tab . '_conditions_{i}_uid_{j}',
                    'name'      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][conditions][{j}][uid]',
                ), false); ?>

                <div class="rp_wcdpd_clear_both"></div>
            </div>

            <div class="rp_wcdpd_condition_remove rp_wcdpd_child_element_remove">
                <div class="rp_wcdpd_condition_remove_handle rp_wcdpd_child_element_remove_handle">
                    <span class="dashicons dashicons-no-alt"></span>
                </div>
            </div>
            <div class="rp_wcdpd_clear_both"></div>
        </div>
    </div>

    <!-- CONDITION FIELDS -->
    <?php foreach(RP_WCDPD_Controller_Conditions::get_items_for_display($current_tab) as $group_key => $group): ?>
        <?php foreach($group['options'] as $option_key => $option): ?>

            <?php $combined_key = $group_key . '__' . $option_key; ?>

            <div id="rp_wcdpd_condition_setting_fields_<?php echo $combined_key ?>_template">
                <div class="rp_wcdpd_condition_setting_fields rp_wcdpd_condition_setting_fields_<?php echo $combined_key ?>">

                    <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'before'); ?>

                    <div class="rp_wcdpd_condition_setting_fields_<?php echo (in_array($combined_key, array('customer__logged_in', 'other__pricing_rules_applied'), true) ? 'triple' : 'single'); ?>">
                        <?php RightPress_Forms::select(array(
                            'id'                        => 'rp_wcdpd_' . $current_tab . '_conditions_{i}_method_option_{j}',
                            'name'                      => 'rp_wcdpd_settings[' . $current_tab . '][{i}][conditions][{j}][method_option]',
                            'class'                     => 'rp_wcdpd_' . $current_tab . '_condition_method rp_wcdpd_child_element_field',
                            'options'                   => RP_WCDPD_Controller_Conditions::get_condition_method_options_for_display($combined_key),
                            'data-rp-wcdpd-validation'  => 'required',
                        )); ?>
                    </div>

                    <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'after'); ?>

                    <div class="rp_wcdpd_clear_both"></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- DISABLED CONDITION -->
    <div id="rp_wcdpd_condition_disabled_template">
        <div class="rp_wcdpd_condition_disabled">
            <div class="rp_wcdpd_condition_disabled_text">
                <?php esc_html_e('Condition type was disabled. Enable it or delete this placeholder after reviewing your settings.', 'rp_wcdpd'); ?>
            </div>
        </div>
    </div>

    <!-- DISABLED CUSTOM TAXONOMY CONDITION -->
    <div id="rp_wcdpd_condition_disabled_taxonomy_template">
        <div class="rp_wcdpd_condition_disabled_taxonomy">
            <div class="rp_wcdpd_condition_disabled_taxonomy_text">
                <?php esc_html_e('Custom taxonomy condition was disabled. Enable it or delete this placeholder after reviewing your settings.', 'rp_wcdpd'); ?>
            </div>
        </div>
    </div>

    <!-- NON EXISTENT CONDITION -->
    <div id="rp_wcdpd_condition_non_existent_template">
        <div class="rp_wcdpd_condition_non_existent">
            <div class="rp_wcdpd_condition_non_existent_text">
                <?php esc_html_e('Condition type no longer exists. Delete this placeholder after reviewing your settings.', 'rp_wcdpd'); ?>
            </div>
        </div>
    </div>

    <!-- NON EXISTENT TAXONOMY CONDITION -->
    <div id="rp_wcdpd_condition_non_existent_taxonomy_template">
        <div class="rp_wcdpd_condition_non_existent_taxonomy">
            <div class="rp_wcdpd_condition_non_existent_taxonomy_text">
                <?php esc_html_e('Custom taxonomy no longer exists. Delete this placeholder after reviewing your settings.', 'rp_wcdpd'); ?>
            </div>
        </div>
    </div>

    <?php if ($current_tab === 'product_pricing'): ?>

        <!-- NO QUANTITY RANGES -->
        <div id="rp_wcdpd_no_quantity_ranges_template">
            <div class="rp_wcdpd_no_quantity_ranges rp_wcdpd_no_child_elements"><?php esc_html_e('No quantity ranges.', 'rp_wcdpd'); ?></div>
        </div>

        <!-- QUANTITY RANGES WRAPPER -->
        <div id="rp_wcdpd_quantity_range_wrapper_template">
            <div class="rp_wcdpd_quantity_range_wrapper"></div>
        </div>

        <!-- QUANTITY RANGE -->
        <div id="rp_wcdpd_quantity_range_template">
            <div class="rp_wcdpd_quantity_range rp_wcdpd_child_element">
                <div class="rp_wcdpd_quantity_range_sort rp_wcdpd_child_element_sort">
                    <div class="rp_wcdpd_quantity_range_sort_handle rp_wcdpd_child_element_sort_handle">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                </div>

                <div class="rp_wcdpd_quantity_range_content rp_wcdpd_child_element_content">

                    <div class="rp_wcdpd_quantity_range_setting">
                        <div class="rp_wcdpd_field rp_wcdpd_field_full">
                            <?php RightPress_Forms::number(array(
                                'id'                        => 'rp_wcdpd_product_pricing_quantity_ranges_{i}_from_{j}',
                                'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][quantity_ranges][{j}][from]',
                                'class'                     => 'rp_wcdpd_product_pricing_quantity_range_from rp_wcdpd_child_element_field',
                                'placeholder'               => esc_html__('From', 'rp_wcdpd'),
                                'data-rp-wcdpd-validation'  => 'required,number_min_1,number_whole',
                            )); ?>
                        </div>
                    </div>

                    <div class="rp_wcdpd_quantity_range_setting">
                        <div class="rp_wcdpd_field rp_wcdpd_field_full">
                            <?php RightPress_Forms::number(array(
                                'id'                        => 'rp_wcdpd_product_pricing_quantity_ranges_{i}_to_{j}',
                                'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][quantity_ranges][{j}][to]',
                                'class'                     => 'rp_wcdpd_product_pricing_quantity_range_to rp_wcdpd_child_element_field',
                                'placeholder'               => esc_html__('To - No limit', 'rp_wcdpd'),
                                'data-rp-wcdpd-validation'  => 'number_min_1,number_whole',
                            )); ?>
                        </div>
                    </div>

                    <div class="rp_wcdpd_quantity_range_setting">
                        <div class="rp_wcdpd_field rp_wcdpd_field_full">
                            <?php RightPress_Forms::grouped_select(array(
                                'id'                        => 'rp_wcdpd_product_pricing_quantity_ranges_{i}_pricing_method_{j}',
                                'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][quantity_ranges][{j}][pricing_method]',
                                'class'                     => 'rp_wcdpd_product_pricing_quantity_range_pricing_method rp_wcdpd_child_element_field',
                                'options'                   => RP_WCDPD_Pricing::get_pricing_methods_for_display('product_pricing_volume'),
                                'data-rp-wcdpd-validation'  => 'required',
                            ), true); ?>
                        </div>
                    </div>

                    <div class="rp_wcdpd_quantity_range_setting">
                        <div class="rp_wcdpd_field rp_wcdpd_field_full">
                            <?php RightPress_Forms::decimal(array(
                                'id'                        => 'rp_wcdpd_product_pricing_quantity_ranges_{i}_pricing_value_{j}',
                                'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][quantity_ranges][{j}][pricing_value]',
                                'class'                     => 'rp_wcdpd_product_pricing_quantity_range_pricing_value rp_wcdpd_child_element_field',
                                'placeholder'               => '0.00',
                                'data-rp-wcdpd-validation'  => 'required,number_min_0',
                            )); ?>
                        </div>
                    </div>

                    <?php RightPress_Forms::hidden(array(
                        'id'        => 'rp_wcdpd_product_pricing_quantity_ranges_{i}_uid_{j}',
                        'name'      => 'rp_wcdpd_settings[product_pricing][{i}][quantity_ranges][{j}][uid]',
                    ), false); ?>

                    <div class="rp_wcdpd_clear_both"></div>

                </div>

                <div class="rp_wcdpd_quantity_range_remove rp_wcdpd_child_element_remove">
                    <div class="rp_wcdpd_quantity_range_remove_handle rp_wcdpd_child_element_remove_handle">
                        <span class="dashicons dashicons-no-alt"></span>
                    </div>
                </div>
                <div class="rp_wcdpd_clear_both"></div>
            </div>
        </div>

        <!-- NO GROUP PRODUCTS -->
        <div id="rp_wcdpd_no_group_products_template">
            <div class="rp_wcdpd_no_group_products rp_wcdpd_no_child_elements"><?php esc_html_e('No products in group.', 'rp_wcdpd'); ?></div>
        </div>

        <!-- GROUP PRODUCTS WRAPPER -->
        <div id="rp_wcdpd_group_product_wrapper_template">
            <div class="rp_wcdpd_group_product_wrapper"></div>
        </div>

        <!-- GROUP PRODUCT -->
        <div id="rp_wcdpd_group_product_template">
            <div class="rp_wcdpd_group_product rp_wcdpd_child_element">
                <div class="rp_wcdpd_group_product_sort rp_wcdpd_child_element_sort">
                    <div class="rp_wcdpd_group_product_sort_handle rp_wcdpd_child_element_sort_handle">
                        <span class="dashicons dashicons-menu"></span>
                    </div>
                </div>

                <div class="rp_wcdpd_group_product_content rp_wcdpd_child_element_content">

                    <div class="rp_wcdpd_group_product_setting rp_wcdpd_group_product_setting_single rp_wcdpd_group_product_setting_quantity">
                        <?php RightPress_Forms::number(array(
                            'id'                        => 'rp_wcdpd_product_pricing_group_products_{i}_quantity_{j}',
                            'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][group_products][{j}][quantity]',
                            'class'                     => 'rp_wcdpd_product_pricing_group_product_quantity rp_wcdpd_child_element_field',
                            'placeholder'               => 'Qty',
                            'data-rp-wcdpd-validation'  => 'required,number_min_1,number_whole',
                        )); ?>
                    </div>

                    <div class="rp_wcdpd_group_product_setting rp_wcdpd_group_product_setting_single rp_wcdpd_group_product_setting_type">
                        <?php RightPress_Forms::grouped_select(array(
                            'id'                        => 'rp_wcdpd_product_pricing_group_products_{i}_type_{j}',
                            'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][group_products][{j}][type]',
                            'class'                     => 'rp_wcdpd_product_pricing_group_product_type rp_wcdpd_child_element_field rightpress_select2 rp_wcdpd_select2 rp_wcdpd_select2_grouped',
                            'options'                   => RP_WCDPD_Controller_Conditions::get_items_for_display('product_pricing_group_product'),
                            'data-rp-wcdpd-validation'  => 'required',
                        ), true); ?>
                    </div>

                    <?php foreach(RP_WCDPD_Controller_Conditions::get_items_for_display('product_pricing_group_product') as $group_key => $group): ?>
                        <?php foreach($group['options'] as $option_key => $option): ?>

                            <?php $combined_key = $group_key . '__' . $option_key; ?>

                            <div class="rp_wcdpd_group_product_setting_fields rp_wcdpd_group_product_setting_fields_<?php echo $combined_key ?>" style="display: none;">

                                <div class="rp_wcdpd_group_product_setting_fields_single">
                                    <?php RightPress_Forms::select(array(
                                        'id'                        => 'rp_wcdpd_product_pricing_group_products_{i}_method_option_{j}',
                                        'name'                      => 'rp_wcdpd_settings[product_pricing][{i}][group_products][{j}][method_option]',
                                        'class'                     => 'rp_wcdpd_product_pricing_group_product_method rp_wcdpd_child_element_field',
                                        'options'                   => RP_WCDPD_Controller_Conditions::get_condition_method_options_for_display($combined_key),
                                        'disabled'                  => 'disabled',
                                        'data-rp-wcdpd-validation'  => 'required',
                                    )); ?>
                                </div>

                                <?php RP_WCDPD_Controller_Conditions::display_fields($current_tab, $combined_key, 'after', 'group_product'); ?>

                                <div class="rp_wcdpd_clear_both"></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                    <?php RightPress_Forms::hidden(array(
                        'id'        => 'rp_wcdpd_product_pricing_group_products_{i}_uid_{j}',
                        'name'      => 'rp_wcdpd_settings[product_pricing][{i}][group_products][{j}][uid]',
                    ), false); ?>

                    <div class="rp_wcdpd_clear_both"></div>

                </div>

                <div class="rp_wcdpd_group_product_remove rp_wcdpd_child_element_remove">
                    <div class="rp_wcdpd_group_product_remove_handle rp_wcdpd_child_element_remove_handle">
                        <span class="dashicons dashicons-no-alt"></span>
                    </div>
                </div>
                <div class="rp_wcdpd_clear_both"></div>

            </div>
        </div>

    <?php endif; ?>

</div>
