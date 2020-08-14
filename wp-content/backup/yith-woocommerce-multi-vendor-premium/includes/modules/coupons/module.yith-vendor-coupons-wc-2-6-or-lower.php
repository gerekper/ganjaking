<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendor_Coupons
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Vendor_Coupons' ) ) {

    /**
     * YITH_Meta_Box_Coupon_Data Class
     */
    class YITH_Vendor_Coupons extends YITH_Abstract_Vendor_Coupons {

        /**
         * Override this method to add other action to __construct
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function extra_construct_action(){
            add_filter( 'yith_wc_multi_vendor_coupon_types', 'YITH_Vendor_Coupons::not_allowed_coupon_types' );
        }

        /**
         * @param $allowed
         * @return array
         */
        public static function not_allowed_coupon_types( $allowed ){
            $not_allowed = array( 'fixed_cart', 'percent' );
            return $not_allowed;
        }

        /**
         * Output the metabox
         */
        public static function output( $post ) {
            wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
            ?>
            <style type="text/css">
                #edit-slug-box, #minor-publishing-actions { display:none }
            </style>
            <div id="coupon_options" class="panel-wrap coupon_data">

                <div class="wc-tabs-back"></div>

                <ul class="coupon_data_tabs wc-tabs" style="display:none;">
                    <?php
                    $coupon_data_tabs = apply_filters( 'woocommerce_coupon_data_tabs', array(
                        'general' => array(
                            'label'  => __( 'General', 'yith-woocommerce-product-vendors' ),
                            'target' => 'general_coupon_data',
                            'class'  => 'general_coupon_data',
                        ),
                        'usage_restriction' => array(
                            'label'  => __( 'Usage Restriction', 'yith-woocommerce-product-vendors' ),
                            'target' => 'usage_restriction_coupon_data',
                            'class'  => '',
                        ),
                        'usage_limit' => array(
                            'label'  => __( 'Usage Limits', 'yith-woocommerce-product-vendors' ),
                            'target' => 'usage_limit_coupon_data',
                            'class'  => '',
                        )
                    ) );

                    foreach ( $coupon_data_tabs as $key => $tab ) {
                        ?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
                        <a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
                        </li><?php
                    }
                    ?>
                </ul>
                <div id="general_coupon_data" class="panel woocommerce_options_panel"><?php

                    // Type
                    woocommerce_wp_select( array( 'id' => 'discount_type', 'label' => __( 'Discount type', 'yith-woocommerce-product-vendors' ), 'options' => wc_get_coupon_types() ) );

                    // Amount
                    woocommerce_wp_text_input( array( 'id' => 'coupon_amount', 'label' => __( 'Coupon amount', 'yith-woocommerce-product-vendors' ), 'placeholder' => wc_format_localized_price( 0 ), 'description' => __( 'Value of the coupon.', 'yith-woocommerce-product-vendors' ), 'data_type' => 'price', 'desc_tip' => true ) );

                    // Expiry date
                    woocommerce_wp_text_input( array( 'id' => 'expiry_date', 'label' => __( 'Coupon expiry date', 'yith-woocommerce-product-vendors' ), 'placeholder' => _x( 'YYYY-MM-DD', 'placeholder', 'yith-woocommerce-product-vendors' ), 'description' => '', 'class' => 'date-picker', 'custom_attributes' => array( 'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" ) ) );

                    do_action( 'woocommerce_coupon_options' );

                    ?></div>
                <div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel"><?php

                    echo '<div class="options_group">';

                    // minimum spend
                    woocommerce_wp_text_input( array( 'id' => 'minimum_amount', 'label' => __( 'Minimum spend', 'yith-woocommerce-product-vendors' ), 'placeholder' => __( 'No minimum', 'yith-woocommerce-product-vendors' ), 'description' => __( 'This field allows you to set the minimum subtotal needed to use the coupon.', 'yith-woocommerce-product-vendors' ), 'data_type' => 'price', 'desc_tip' => true ) );

                    // maximum spend
                    woocommerce_wp_text_input( array( 'id' => 'maximum_amount', 'label' => __( 'Maximum spend', 'yith-woocommerce-product-vendors' ), 'placeholder' => __( 'No maximum', 'yith-woocommerce-product-vendors' ), 'description' => __( 'This field allows you to set the maximum subtotal allowed when using the coupon.', 'yith-woocommerce-product-vendors' ), 'data_type' => 'price', 'desc_tip' => true ) );

                    // Individual use
                    woocommerce_wp_checkbox( array( 'id' => 'individual_use', 'label' => __( 'Individual use only', 'yith-woocommerce-product-vendors' ), 'description' => __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'yith-woocommerce-product-vendors' ) ) );

                    // Exclude Sale Products
                    woocommerce_wp_checkbox( array( 'id' => 'exclude_sale_items', 'label' => __( 'Exclude sale items', 'yith-woocommerce-product-vendors' ), 'description' => __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'yith-woocommerce-product-vendors' ) ) );

                    echo '</div><div class="options_group">';

                    // Product ids
                    ?>
                    <p class="form-field"><label><?php _e( 'Products', 'yith-woocommerce-product-vendors' ); ?></label>
                        <input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="product_ids" data-placeholder="<?php _e( 'Search for a product&hellip;', 'yith-woocommerce-product-vendors' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="<?php
                        $product_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'product_ids', true ) ) ) );
                        $json_ids    = array();

                        foreach ( $product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                            }
                        }

                        echo esc_attr( json_encode( $json_ids ) );
                        ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> <img class="help_tip" data-tip='<?php _e( 'Select products that must have been added to cart to use this coupon.', 'yith-woocommerce-product-vendors' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
                    <?php

                    // Exclude Product ids
                    ?>
                    <p class="form-field"><label><?php _e( 'Exclude products', 'yith-woocommerce-product-vendors' ); ?></label>
                        <input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="exclude_product_ids" data-placeholder="<?php _e( 'Search for a product&hellip;', 'yith-woocommerce-product-vendors' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="<?php
                        $product_ids = array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'exclude_product_ids', true ) ) ) );
                        $json_ids    = array();

                        foreach ( $product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                            }
                        }

                        echo esc_attr( json_encode( $json_ids ) );
                        ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> <img class="help_tip" data-tip='<?php _e( 'Select products that must have been added to cart to use this coupon.', 'yith-woocommerce-product-vendors' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
                    <?php

                    echo '</div><div class="options_group">';

                    // Customers
                    woocommerce_wp_text_input( array( 'id' => 'customer_email', 'label' => __( 'Email restrictions', 'yith-woocommerce-product-vendors' ), 'placeholder' => __( 'No restrictions', 'yith-woocommerce-product-vendors' ), 'description' => __( 'List of allowed emails to check against the customer\'s billing email when an order is placed. Separate email addresses with commas.', 'yith-woocommerce-product-vendors' ), 'value' => implode(', ', (array) get_post_meta( $post->ID, 'customer_email', true ) ), 'desc_tip' => true, 'type' => 'email', 'class' => '', 'custom_attributes' => array(
                        'multiple' 	=> 'multiple'
                    ) ) );

                    echo '</div>';

                    do_action( 'woocommerce_coupon_options_usage_restriction' );

                    ?></div>
                <div id="usage_limit_coupon_data" class="panel woocommerce_options_panel"><?php

                    echo '<div class="options_group">';

                    // Usage limit per coupons
                    woocommerce_wp_text_input( array( 'id' => 'usage_limit', 'label' => __( 'Usage limit per coupon', 'yith-woocommerce-product-vendors' ), 'placeholder' => _x('Unlimited usage', 'placeholder', 'yith-woocommerce-product-vendors'), 'description' => __( 'How many times this coupon can be used before it is void.', 'yith-woocommerce-product-vendors' ), 'type' => 'number', 'desc_tip' => true, 'class' => 'short', 'custom_attributes' => array(
                        'step' 	=> '1',
                        'min'	=> '0'
                    ) ) );

                    // Usage limit per product
                    woocommerce_wp_text_input( array( 'id' => 'limit_usage_to_x_items', 'label' => __( 'Limit usage to X items', 'yith-woocommerce-product-vendors' ), 'placeholder' => _x( 'Apply to all qualifying items in cart', 'placeholder', 'yith-woocommerce-product-vendors' ), 'description' => __( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'yith-woocommerce-product-vendors' ), 'desc_tip' => true, 'class' => 'short', 'type' => 'number', 'custom_attributes' => array(
                        'step' 	=> '1',
                        'min'	=> '0'
                    ) ) );

                    // Usage limit per users
                    woocommerce_wp_text_input( array( 'id' => 'usage_limit_per_user', 'label' => __( 'Usage limit per user', 'yith-woocommerce-product-vendors' ), 'placeholder' => _x( 'Unlimited usage', 'placeholder', 'yith-woocommerce-product-vendors' ), 'description' => __( 'How many times this coupon can be used by an invidual user. Uses billing email for guests, and user ID for logged in users.', 'yith-woocommerce-product-vendors' ), 'desc_tip' => true, 'class' => 'short', 'type' => 'number', 'custom_attributes' => array(
                        'step' 	=> '1',
                        'min'	=> '0'
                    ) ) );

                    echo '</div>';

                    do_action( 'woocommerce_coupon_options_usage_limit' );

                    ?></div>
                <?php do_action( 'woocommerce_coupon_data_panels' ); ?>
                <div class="clear"></div>
            </div>
            <?php
        }
    }
}

/**
 * Main instance of plugin
 *
 * @return YITH_Commissions
 * @since  1.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Vendor_Coupons' ) ) {
    function YITH_Vendor_Coupons() {
        return YITH_Abstract_Vendor_Coupons::instance( 'YITH_Vendor_Coupons' );
    }
}

YITH_Vendor_Coupons();
