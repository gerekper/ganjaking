<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @var YITH_Vendor $vendor
 */

$vendor_enable_shipping = checked( 'yes', $vendor->enable_shipping , false);
$vendor_shipping_default_price = ! empty( $vendor->shipping_default_price ) ? $vendor->shipping_default_price : '';
$vendor_shipping_product_additional_price = ! empty( $vendor->shipping_product_additional_price ) ? $vendor->shipping_product_additional_price : '';
$vendor_shipping_product_qty_price = ! empty( $vendor->shipping_product_qty_price ) ? $vendor->shipping_product_qty_price : '';

$vendor_processing_times = YITH_Vendor_Shipping::yith_wcmv_get_shipping_processing_times();
$vendor_shipping_processing_time =  ! empty( $vendor->shipping_processing_time ) ? $vendor->shipping_processing_time : '';

$vendor_shipping_policy = !empty( $vendor->shipping_policy ) ? $vendor->shipping_policy : '';
$vendor_shipping_refund_policy = !empty( $vendor->shipping_refund_policy ) ? $vendor->shipping_refund_policy : '';

$wc_country_obj     = new WC_Countries();
$wc_countries       = $wc_country_obj->countries;
$wc_states          = $wc_country_obj->states;

$vendor_shipping_location_from = !empty( $vendor->shipping_location_from ) ? $vendor->shipping_location_from : '';

?>
<div class="wrap yith-vendor-admin-wrap" id="vendor-details">
    <form method="post" action="<?php echo apply_filters( 'yith_wcmv_vendor_panel_form_action', admin_url( 'admin.php' ) ); ?>" enctype="multipart/form-data" class="yith_admin_vendor_shipping">
        <h3><?php _e( 'Shipping Settings', 'yith-woocommerce-product-vendors' ) ?></h3>

        <input type="hidden" name="update_vendor_id" value="<?php echo $vendor->id ?>" />
        <input type="hidden" name="action" value="yith_admin_save_fields" />
        <input type="hidden" name="page" value="<?php echo ! empty( $_GET['page'] ) ? $_GET['page'] : '' ?>" />
        <input type="hidden" name="tab" value="<?php echo ! empty( $_GET['tab'] ) ? $_GET['tab'] : '' ?>" />

        <?php echo wp_nonce_field( 'yith_vendor_admin_update', 'yith_vendor_admin_update_nonce', true, false ) ?>

        <div class="form-field checkbox">
            <label for="vendor_enable_shipping"><?php  _e( 'Enable shipping:', 'yith-woocommerce-product-vendors' ) ?></label>
            <input type="checkbox" id="vendor_enable_shipping" name="yith_vendor_data[enable_shipping]" value="yes" <?php echo $vendor_enable_shipping ?> />
            <span class="description"><?php _e( "Enable shipping cost", 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_default_price"><?php  _e( 'Default Shipping Price:', 'yith-woocommerce-product-vendors' ) ?></label>
            <input id="vendor_shipping_default_price" type="number" placeholder="0.00"  name="yith_vendor_data[shipping_default_price]" step="any" min="0" class="regular-text" value="<?php echo $vendor_shipping_default_price; ?>" />
            <span class="description"><?php _e( 'The default shipping price for each product in the cart.', 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_product_additional_price"><?php  _e( 'Per Product Additional Price:', 'yith-woocommerce-product-vendors' ) ?></label>
            <input id="vendor_shipping_product_additional_price" type="number" placeholder="0.00"  name="yith_vendor_data[shipping_product_additional_price]" step="any" min="0" class="regular-text" value="<?php echo $vendor_shipping_product_additional_price; ?>" />
            <span class="description"><?php _e( 'Additional price for each product if product quantity in cart is equal or greater than 2.', 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_product_qty_price"><?php  _e( 'Per Qty Product Additional Price:', 'yith-woocommerce-product-vendors' ) ?></label>
            <input id="vendor_shipping_product_qty_price" type="number" placeholder="0.00"  name="yith_vendor_data[shipping_product_qty_price]" step="any" min="0" class="regular-text" value="<?php echo $vendor_shipping_product_qty_price; ?>" />
            <span class="description"><?php _e( 'The additional price from the second product of the same type', 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_processing_time"><?php  _e( 'Processing Time', 'yith-woocommerce-product-vendors' ) ?></label>
            <select id="vendor_shipping_processing_time" name="yith_vendor_data[shipping_processing_time]" >
                <?php foreach ( $vendor_processing_times as $processing_key => $processing_value ): ?>
                    <option value="<?php echo $processing_key; ?>" <?php selected( $vendor_shipping_processing_time, $processing_key ); ?>><?php echo $processing_value; ?></option>
                <?php endforeach ?>
            </select>
            <span class="description"><?php _e( 'The time required before sending the product for delivery', 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_policy"><?php  _e( 'Shipping Policy:', 'yith-woocommerce-product-vendors' ) ?></label>
            <textarea id="vendor_shipping_policy" name="yith_vendor_data[shipping_policy]" rows="10" cols="50" style="width: 400px;" class="regular-text"><?php echo esc_textarea( stripslashes( $vendor_shipping_policy ) ) ?></textarea>
            <br/>
            <span class="description"><?php _e( "You terms, conditions and instructions about shipping", 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_refund_policy"><?php  _e( 'Refund Policy:', 'yith-woocommerce-product-vendors' ) ?></label>
            <textarea id="vendor_shipping_refund_policy" name="yith_vendor_data[shipping_refund_policy]" rows="10" cols="50" style="width: 400px;" class="regular-text"><?php echo esc_textarea( stripslashes( $vendor_shipping_refund_policy ) ) ?></textarea>
            <br/>
            <span class="description"><?php _e( "You terms, conditions and instructions about refund", 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field">
            <label for="vendor_shipping_location_from"><?php  _e( 'Shipping From', 'yith-woocommerce-product-vendors' ) ?></label>
            <select id="vendor_shipping_location_from" name="yith_vendor_data[shipping_location_from]" >
                <?php printf( '<option value="">%s</option>', __( '- Select a location -', 'yith-woocommerce-product-vendors' ) ); ?>
                <?php foreach ( $wc_countries as $country_key => $country_value ): ?>
                    <option value="<?php echo $country_key; ?>" <?php selected( $vendor_shipping_location_from, $country_key ); ?>><?php echo $country_value; ?></option>
                <?php endforeach ?>
            </select>
            <span class="description"><?php _e( 'Location from where the product are shipped for delivery', 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <?php YITH_Vendor_Shipping()->admin->print_shipping_table( $vendor , $wc_country_obj );  ?>

        <div class="submit">
            <input id="yith-save-shipping-settings-button" name="Submit" type="submit" class="button-primary" value="<?php echo esc_attr( __( 'Save Shipping Settings', 'yith-woocommerce-product-vendors' ) ) ?>" />
            <button id="yith-wpv-shipping-metohd-btn-add" class="button-secondary"><?php _e( 'Add shipping zone', 'yith-woocommerce-product-vendors' ); ?></button>
        </div>


    </form>
</div>