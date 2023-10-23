<?php
/**
 * YITH_WAPO_Addon_Premium Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.1.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Addon_Premium' ) ) {

    /**
     *  Addon class.
     *  The class manage all the Addon behaviors.
     */
    class YITH_WAPO_Addon_Premium extends YITH_WAPO_Addon {


        /**
         *  Constructor
         *
         * @param array $args The args to instantiate the class.
         */
        public function __construct( $args ) {
            parent::__construct( $args );
        }

        /**
         * @param int $index The index of the add-on.
         * @return array
         */
        public function create_availability_time_array( $index ) {
            $times       = array();
            $time_format = wc_string_to_bool( get_option( 'yith_wapo_enable_24_hour_format', 'no' ) );

            $time_format = apply_filters( 'yith_wapo_timepicker_24_hours_format', $time_format, $this, $index );
            $format      = $time_format ? 24 : 12; // Default 12.

            $enable_time_slots   = wc_string_to_bool( $this->get_option( 'enable_time_slots', $index ) );
            $time_interval       = $this->get_option( 'time_interval', $index );
            $time_interval_type  = $this->get_option( 'time_interval_type', $index );

            $interval = $time_interval . ' ' . $time_interval_type;

            $initial_time = '00:00';
            $final_time   = '23:59';

            if ( $enable_time_slots ) {
                $time_slots_type     = $this->get_option( 'time_slots_type', $index ); // enable/disable
                $time_slot_from      = $this->get_option( 'time_slot_from', $index );
                $time_slot_from_min  = $this->get_option( 'time_slot_from_min', $index );
                $time_slot_from_type = $this->get_option( 'time_slot_from_type', $index );
                $time_slot_to        = $this->get_option( 'time_slot_to', $index );
                $time_slot_to_min    = $this->get_option( 'time_slot_to_min', $index );
                $time_slot_to_type   = $this->get_option( 'time_slot_to_type', $index );

                for ( $i = 0; $i < count( $time_slot_from ); $i++ ) {
                    $start_hour    = sprintf("%02d", $time_slot_from[$i] );
                    $start_minutes = sprintf("%02d", $time_slot_from_min[$i] );
                    $end_hour      = sprintf("%02d", $time_slot_to[$i] );
                    $end_minutes   = sprintf("%02d", $time_slot_to_min[$i] );

                    $start_time   = $start_hour . ':' . $start_minutes . $time_slot_from_type[$i];
                    $end_time     = $end_hour . ':' . $end_minutes . $time_slot_to_type[$i];

                    $times_arr = yith_wapo_create_time_range( $start_time, $end_time, $time_interval_type, $interval, $format );
                    $times     = array_merge( $times, $times_arr );
                }

                if ( 'disable' === $time_slots_type ) {
                    $all_times = yith_wapo_create_time_range( $start_time, $end_time, $time_interval_type, $interval, $format );
                    $times     = array_diff( $all_times, $times );
                }

            } else {
                $times = yith_wapo_create_time_range( $initial_time, $final_time, $time_interval_type, $interval, $format );

            }

            return array_unique( $times );
        }

        /**
         * Get the array of options of the Configuration tab
         *
         * @return array
         */
        public function get_options_configuration_array(){

            $options = array();

            if ( ! empty( $this ) ) {
                $selection_type         = $this->get_setting( 'selection_type', 'single', false );
                $first_options_selected = $this->get_setting( 'first_options_selected', 'no', false );
                $first_free_options     = $this->get_setting( 'first_free_options', 0, false );
                $enable_min_max         = $this->get_setting( 'enable_min_max', 'no', false );
                $min_max_rule           = (array) $this->get_setting( 'min_max_rule', 'min', false );
                $min_max_value          = (array) $this->get_setting( 'min_max_value', 0, false );
                $enable_min_max_numbers = $this->get_setting( 'enable_min_max_numbers', 'no', false );
                $sell_individually      = $this->get_setting( 'sell_individually', 'no', false );
                $required               = $this->get_setting( 'required', 'no', false );
                $numbers_min            = $this->get_setting( 'numbers_min', '', false );
                $numbers_max            = $this->get_setting( 'numbers_max', '', false );


                $options = array(
                    'addon-selection-type' => array(
                        // translators: [ADMIN] Add-on editor > Options configuration option
                        'title' => __( 'Selection type', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name' => 'addon_selection_type',
                                'type' => 'radio',
                                'value' => $selection_type,
                                'options' => array(
                                    'single'     => yith_wapo_get_string_by_addon_type( 'single_option', $this->type ),
                                    'multiple'   => yith_wapo_get_string_by_addon_type( 'multiple_options', $this->type ),
                                ),
                            ),
                        ),
                        //translators: %1$s is the string "select", "fill", etc. depending on add-on type. %2$s is the string "option" depending on add-on type.
                        'description' => yith_wapo_get_string_by_addon_type( 'selection_description', $this->type ),
                    ),
                    'addon-first-options-selected' => array(
                        'title' => yith_wapo_get_string_by_addon_type( 'first_options', $this->type ),
                        'field' => array(
                            array(
                                'name' => 'addon_first_options_selected',
                                'class' => 'enabler',
                                'type' => 'onoff',
                                'value' => $first_options_selected,
                            ),
                        ),
                        'description' => yith_wapo_get_string_by_addon_type( 'first_options_description', $this->type ),
                    ),
                    'addon-first-free-options' => array(
                        'enabled-by' => 'addon-first-options-selected',
                        // translators: %s is the string "select", "fill", etc. depending on add-on type.
                        'title' => yith_wapo_get_string_by_addon_type( 'select_free', $this->type ),
                        'field' => array(
                            array(
                                'name' => 'addon_first_free_options',
                                'type' => 'number',
                                'value' => $first_free_options,
                                'custom_message' => yith_wapo_get_string_by_addon_type( 'options', $this->type )
                            ),
                        ),
                        'description' => yith_wapo_get_string_by_addon_type( 'can_select_for_free', $this->type ),
                    ),
                    'addon-enable-min-max' => array(
                        'title' => yith_wapo_get_string_by_addon_type( 'force_select', $this->type ),
                        'field' => array(
                            array(
                                'name'  => 'addon_enable_min_max',
                                'class' => 'enabler',
                                'type'  => 'onoff',
                                'value' => $enable_min_max,
                            ),
                        ),
                        'description' => yith_wapo_get_string_by_addon_type( 'force_select_description', $this->type ),
                    ),
                    'addon-min-exa-rules' => array(
                        'enabled-by'  => 'addon-enable-min-max',
                        'title'       => yith_wapo_get_string_by_addon_type( 'proceed_purchase', $this->type ),
                        'description' => yith_wapo_get_string_by_addon_type( 'proceed_purchase_description', $this->type ),
                    ),
                    'addon-max-rule' => array(
                        'title'       => yith_wapo_get_string_by_addon_type( 'can_select_max', $this->type ),
                        'description' => yith_wapo_get_string_by_addon_type( 'can_select_max_description', $this->type ),
                    ),
                    'addon-required' => array(
                        // translators: [ADMIN] Add-on editor > Options configuration option
                        'title' => __( 'Force user to select an option', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'    => 'addon_required',
                                'class'   => 'yith-wapo-required-select',
                                'type'    => 'onoff',
                                'default' => 'no',
                                'value'   => $required,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Options configuration option
                        'description' => __( 'Enable to force the user to select an option of the select to proceed with the purchase.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-enable-min-max-all' => array(
                        'title' => yith_wapo_get_string_by_addon_type( 'min_max_all', $this->type ),
                        'field' => array(
                            array(
                                'name'  => 'addon_enable_min_max_numbers',
                                'class' => 'enabler',
                                'type'  => 'onoff',
                                'value' => $enable_min_max_numbers,
                            )
                        ),
                        'description' => yith_wapo_get_string_by_addon_type( 'min_max_all_description', $this->type ),
                    ),
                    'min-max-number' => array(
                        'enabled-by' => 'addon-enable-min-max-all',
                        'title'      => yith_wapo_get_string_by_addon_type( 'min_max_number', $this->type ),
                        'div-class'  => 'min-max-numbers',
                        'field' => array(
                            array(
                                // translators: [ADMIN] Add-on editor > Options configuration option
                                'title'     => _x( 'MIN', 'Minimum value', 'yith-woocommerce-product-add-ons' ),
                                'div-class' => 'min-number min-max-number',
                                'name'      => 'addon_number_min',
                                'type'      => 'number',
                                'value'     => $numbers_min,
                            ),
                            array(
                                // translators: [ADMIN] Add-on editor > Options configuration option
                                'title'     => _x( 'MAX', 'Maximum value', 'yith-woocommerce-product-add-ons' ),
                                'div-class' => 'max-number min-max-number',
                                'name'      => 'addon_number_max',
                                'type'      => 'number',
                                'value'     => $numbers_max,
                            )
                        ),
                    ),
                    'addon-sell-individually' => array(
                        // translators: [ADMIN] Add-on editor > Options configuration option
                        'title' => __( 'Sell options individually', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_sell_individually',
                                'type'  => 'onoff',
                                'value' => $sell_individually,
                            )
                        ),
                        // translators: [ADMIN] Add-on editor > Options configuration option (description)
                        'description' => __( 'Enable to sell options individually.
						The options are added to the cart in a separate row and their prices are not affected by the quantity of products purchased.',
                            'yith-woocommerce-product-add-ons' ),
                    )
                );
            }

            return $this->get_options_by_addon_type( $options, $this->type, 'configuration' );

        }

        /**
         * Get the array of options of the Display & Style tab
         *
         * @return array
         */
        public function get_options_display_style_array(){
            $options = array();

            if ( ! empty( $this ) ) {

                $options_per_row_default = 1;
                if ( in_array( $this->type, array( 'label', 'color' ) ) ) {
                    $options_per_row_default = 5;
                }

                $show_image             = $this->get_setting( 'show_image', 'no', false );
                $image                  = $this->get_setting( 'image', '' );
                $image_replacement      = $this->get_setting( 'image_replacement', 'no', false );
                $images_position        = $this->get_setting( 'options_images_position', 'above', false );
                $show_as_toggle         = $this->get_setting( 'show_as_toggle', 'no', false );
                $hide_product_prices    = $this->get_setting( 'hide_products_prices', 'no', false );
                $show_sku               = $this->get_setting( 'show_sku', 'no', false );
                $show_stock             = $this->get_setting( 'show_stock', 'no', false );
                $show_add_to_cart       = $this->get_setting( 'show_add_to_cart', 'no', false );
                $show_quantity          = $this->get_setting( 'show_quantity', 'no', false );
                $hide_options_images    = $this->get_setting( 'hide_options_images', 'no', false );
                $hide_options_label     = $this->get_setting( 'hide_options_label', 'no', false );
                $hide_options_prices    = $this->get_setting( 'hide_options_prices', 'no', false );
                $product_out_of_stock   = $this->get_setting( 'product_out_of_stock', 'disable', false );
                $options_per_row        = $this->get_setting( 'options_per_row', $options_per_row_default );
                $show_in_a_grid         = $this->get_setting( 'show_in_a_grid', 'no', false );
                $options_width          = $this->get_setting( 'options_width', 100 );
                $select_width           = $this->get_setting( 'select_width', 75 );
                $show_quantity_selector = $this->get_setting( 'show_quantity_selector', 'no', false );
                $label_content_align     = $this->get_setting( 'label_content_align', 'center', false );
                $image_equal_height      = $this->get_setting( 'image_equal_height', 'no', false );
                $images_height           = $this->get_setting( 'images_height', 100 );
                $label_position          = $this->get_setting( 'label_position', 'default', false );
                $description_position    = $this->get_setting( 'description_position', 'default', false );

                $dimensions_array_default   = array(
                    'dimensions' => array(
                        'top'    => 10,
                        'right'  => 10,
                        'bottom' => 10,
                        'left'   => 10,
                    ),
                );
                $label_padding_defaults = get_option( 'yith_wapo_style_label_padding', $dimensions_array_default )['dimensions'];
                $label_padding_array     = $this->get_setting(
                    'label_padding',
                    array(
                        'dimensions' => array(
                            'top'    => $label_padding_defaults['top'],
                            'right'  => $label_padding_defaults['right'],
                            'bottom' => $label_padding_defaults['bottom'],
                            'left'   => $label_padding_defaults['left'],
                        ),
                    )
                );

                $images_position_array = array(
                    // translators: [ADMIN] Add-on editor > Display & Style option
                    'above' => __( 'Above label', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on editor > Display & Style option
                    'under' => __( 'Under label', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on editor > Display & Style option
                    'left'  => __( 'Left side', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on editor > Display & Style option
                    'right' => __( 'Right side', 'yith-woocommerce-product-add-ons' ),
                );

                if ( 'label' === $this->type ) {
                    $images_position_array = array_merge(
                        array(
                            'default' => _x( 'Default', '[ADMIN] Add-on editor > Display & Style option (Only Label)', 'yith-woocommerce-product-add-ons' )
                        ),
                        $images_position_array
                    );
                }

                $options = array(
                    'addon-show-image' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option
                        'title' => __( 'Show an image for this set of options', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name' => 'addon_show_image',
                                'type' => 'onoff',
                                'class' => 'enabler',
                                'value' => $show_image,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable to show an additional image or icon near the title.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-image' => array(
                        'enabled-by' => 'addon-show-image',
                        // translators: [ADMIN] Add-on editor > Display & Style option
                        'title'      => __( 'Options set image', 'yith-woocommerce-product-add-ons' ),
                        'field'      => array(
                            array(
                                'name'  => 'addon_image',
                                'type'  => 'media',
                                'value' => $image,
                            ),
                        ),
                    ),
                    'addon-image-replacement' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option
                        'title'     => __( 'Product image replacing options', 'yith-woocommerce-product-add-ons' ),
                        'div-class' => '',
                        'field'     => array(
                            array(
                                'name' => 'addon_image_replacement',
                                'class' => 'wc-enhanced-select',
                                'type' => 'select',
                                'value' => $image_replacement,
                                'options' => array(
                                    // translators: [ADMIN] Add-on editor > Display & Style option
                                    'no'      => __( 'Don\'t replace the image', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] Add-on editor > Display & Style option
                                    'addon'   => __( 'Replace with block image', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] Add-on editor > Display & Style option
                                    'options' => __( 'Replace with options images', 'yith-woocommerce-product-add-ons' ),
                                ),
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option
                        'description' => __( 'Choose to replace the default product image when an option is selected and which image to use to replace it.',
                            'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-hide-options-images' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option
                        'title' => __( 'Hide options images', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_hide_options_images',
                                'type'  => 'onoff',
                                'class' => 'enabler revert',
                                'value' => $hide_options_images,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable to hide the options images.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-options-images-position' => array(
                        'enabled-by' => 'addon-hide-options-images',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Options images position', 'yith-woocommerce-product-add-ons' ),
                        'field'      => array(
                            array(
                                'name'    => 'addon_options_images_position',
                                'class'   => 'wc-enhanced-select',
                                'type'    => 'select',
                                'value'   => $images_position,
                                'options' => $images_position_array,
                                'default' => 'above',
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Choose the position of the options images.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-show-as-toggle' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Show as toggle', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_show_as_toggle',
                                'class' => 'wc-enhanced-select',
                                'type'  => 'select',
                                'value' => $show_as_toggle,
                                'options' => array(
                                    // translators: [ADMIN] Add-on editor > Display & Style option (option)
                                    'no'     => __( 'Default', 'yith-woocommerce-product-add-ons' ),
                                    'no-toggle' => __( 'No', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] Add-on editor > Display & Style option (option)
                                    'open'   => __( 'Yes, with toggle opened by default', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] Add-on editor > Display & Style option (option)
                                    'closed' => __( 'Yes, with toggle closed by default', 'yith-woocommerce-product-add-ons' ),
                                ),
                                'default' => 'no',

                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Choose whether to show options in a toggle section.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-hide-products-prices' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Hide product prices', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name' => 'addon_hide_products_prices',
                                'type' => 'onoff',
                                'value' => $hide_product_prices,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable if you want to hide the product prices.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-show-sku' => array(
                        'enabled-by' => '',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Show SKU label', 'yith-woocommerce-product-add-ons' ),
                        'div-class'  => '',
                        'field' => array(
                            array(
                                'name'  => 'addon_show_sku',
                                'type'  => 'onoff',
                                'value' => $show_sku,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable if you want to show the sku label.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-show-stock' => array(
                        'enabled-by' => '',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Show stock label', 'yith-woocommerce-product-add-ons' ),
                        'div-class'  => '',
                        'field'      => array(
                            array(
                                'name' => 'addon_show_stock',
                                'type' => 'onoff',
                                'value' => $show_stock,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable if you want to show the stock label.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-show-add-to-cart' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Show "Add to cart" button', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_show_add_to_cart',
                                'type'  => 'onoff',
                                'value' => $show_add_to_cart,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable if you want to show the "Add to cart" button.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-show-quantity' => array(
                        'enabled-by' => '',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Show quantity selector', 'yith-woocommerce-product-add-ons' ),
                        'div-class'  => '',
                        'field'      => array(
                            array(
                                'name'  => 'addon_show_quantity',
                                'type'  => 'onoff',
                                'value' => $show_quantity,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable if you want to show the quantity selector.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-product-out-of-stock' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'When a product is out of stock', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'    => 'addon_product_out_of_stock',
                                'class'   => 'wc-enhanced-select',
                                'type'    => 'select',
                                'value'   => $product_out_of_stock,
                                'options' => array(
                                    // translators: [ADMIN] Add-on editor > Display & Style option (option)
                                    'hide'    => __( 'Hide product', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] Add-on editor > Display & Style option (option)
                                    'disable' => __( 'Disable product', 'yith-woocommerce-product-add-ons' ),
                                ),
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Choose if hide the out of stock products or not.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-hide-options-label' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Hide labels', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_hide_options_label',
                                'type'  => 'onoff',
                                'value' => $hide_options_label,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable to hide the options labels.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-hide-options-prices' => array(
                        'enabled-by' => '',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Hide prices', 'yith-woocommerce-product-add-ons' ),
                        'div-class'  => '',
                        'field' => array(
                            array(
                                'name'  => 'addon_hide_options_prices',
                                'type'  => 'onoff',
                                'value' => $hide_options_prices,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enable to hide the options prices.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-options-per-row' => array(
                        'enabled-by' => '',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Options per row', 'yith-woocommerce-product-add-ons' ),
                        'div-class'  => '',
                        'field'      => array(
                            array(
                                'name'  => 'addon_options_per_row',
                                'type'  => 'slider',
                                'min'   => 1,
                                'max'   => 10,
                                'step'  => 1,
                                'value' => $options_per_row,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => __( 'Enter how many options to display for each row.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-show-in-a-grid' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Adjust options in a grid', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'class' => 'enabler',
                                'name'  => 'addon_show_in_a_grid',
                                'type'  => 'onoff',
                                'value' => $show_in_a_grid,
                            ),
                        ),
                        //translators: [ADMIN] Add-on editor > Display & Style option (description). %s is a line break.
                        'description' => sprintf( __( 'Enable to adjust the options in a grid based on the page width. %s Using a grid layout, all options will have the same width.', 'yith-woocommerce-product-add-ons' ), '<br>' ),
                    ),
                    'addon-options-width' => array(
                        'enabled-by' => 'addon-show-in-a-grid',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title'      => __( 'Options width', 'yith-woocommerce-product-add-ons' ),
                        'div-class'  => '',
                        'field'      => array(
                            array(
                                'name' => 'addon_options_width',
                                'type'  => 'slider',
                                'min'   => 1,
                                'max'   => 100,
                                'step'  => 1,
                                'value' => $options_width,
                            ),
                        ),
                        // translators: [ADMIN] Add-on editor > Display & Style option (description)
                        'description' => _x( 'Set the width of the options in relation to the container width.', '[ADMIN] Add-on editor > Display & Style option (description)', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-select-width' => array(
                        // translators: [ADMIN] *Only add-on type Selector* Add-on editor > Display & Style option (title)
                        'title' => __( 'Select width (%)', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_select_width',
                                'type'  => 'slider',
                                'min'   => 1,
                                'max'   => 100,
                                'step'  => 1,
                                'value' => $select_width,
                            ),
                        ),
                        // translators: [ADMIN] *Only add-on type Selector* Add-on editor > Display & Style option (description)
                        'description' => __( 'Set the width of the select in relation to the container width.', 'yith-woocommerce-product-add-ons' ),
                    ),
                    'addon-label-content-align' => array(
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Content alignment', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'    => 'addon_label_content_align',
                                'type'    => 'select',
                                'class'   => 'wc-enhanced-select',
                                'value'   => $label_content_align,
                                'options' => array(
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (option)
                                    'left'    => __( 'Left', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (option)
                                    'center'  => __( 'Center', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (option)
                                    'right'   => __( 'Right', 'yith-woocommerce-product-add-ons' ),
                                ),
                            ),
                        ),
                    ),
                    'addon-image-equal-height' => array(
                        'enabled-by' => 'addon-hide-options-images',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Force image equal heights', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_image_equal_height',
                                'class' => 'enabler',
                                'type'  => 'onoff',
                                'value' => $image_equal_height,
                            ),
                        ),
                    ),
                    'addon-images-height' => array(
                        'enabled-by' => 'addon-image-equal-height',
                        // translators: [ADMIN] Add-on editor > Display & Style option (title)
                        'title' => __( 'Image heights (px)', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'  => 'addon_images_height',
                                'type'  => 'number',
                                'min'   => 0,
                                'value' => $images_height,
                            ),
                        ),
                    ),
                    'addon-label-position' => array(
                        // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (title)
                        'title' => __( 'Label position', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'    => 'addon_label_position',
                                'type'    => 'select',
                                'class'   => 'wc-enhanced-select',
                                'value'   => $label_position,
                                'options' => array(
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (title)
                                    'default' => __( 'Default', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (title)
                                    'inside'  => __( 'Inside borders', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (title)
                                    'outside' => __( 'Outside borders', 'yith-woocommerce-product-add-ons' ),
                                ),
                            ),
                        ),
                    ),
                    'addon-description-position' => array(
                        // translators: [ADMIN] *Only add-on type Labels*  Add-on editor > Display & Style option (title)
                        'title' => __( 'Description position', 'yith-woocommerce-product-add-ons' ),
                        'field' => array(
                            array(
                                'name'    => 'addon_description_position',
                                'type'    => 'select',
                                'class'   => 'wc-enhanced-select',
                                'value'   => $description_position,
                                'options' => array(
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (option)
                                    'default' => __( 'Default', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (option)
                                    'inside'  => __( 'Inside borders', 'yith-woocommerce-product-add-ons' ),
                                    // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (option)
                                    'outside' => __( 'Outside borders', 'yith-woocommerce-product-add-ons' ),
                                ),
                            ),
                        ),
                    ),
                    'addon-label-padding' => array(
                        // translators: [ADMIN] *Only add-on type Labels* Add-on editor > Display & Style option (title)
                        'title'      => __( 'Padding', 'yith-woocommerce-product-add-ons' ) . ' (px)',
                        'div-class'  => 'yith-wapo-addon-label-padding',
                        'field'      => array(
                            array(
                                'name'  => 'addon_label_padding',
                                'type'  => 'dimensions',
                                'units' => array(),
                                'value' => $label_padding_array,
                            ),
                        ),
                    ),
                );
            }

            return $this->get_options_by_addon_type( $options, $this->type, 'style' );

        }

        /**
         * Return add-on grid rules.
         *
         * @return string
         */
        public function get_grid_rules() {
            $grid = '
              display: grid;
            ';

            $per_row   = $this->get_setting( 'options_per_row', 1, false );
            $show_grid = wc_string_to_bool( $this->get_setting( 'show_in_a_grid', 'no', false ) );
            $width     = $this->get_setting( 'options_width', 100, false );

            $width = $show_grid ? $width : ( $per_row > 1 ? 100 : 50 );
            $width_percentage = $width / $per_row;

            if ( $show_grid ) {
                if ( $per_row > 1 ) {
                    $grid .= '
                  justify-content: start;
                  grid-template-columns: repeat(' . $per_row . ', minmax(0, ' . $width_percentage . '%) );
                  gap: 10px;
            '   ;
                } else {
                    $grid .= '
                gap: 10px;
                ';
                }
            } else {
                $grid .= '
                    grid-template-columns: repeat(' . $per_row . ', minmax(0, ' . $width_percentage . '%) );
                    gap: 10px;
                    ';
                }

            return $grid;
        }

    }

}
