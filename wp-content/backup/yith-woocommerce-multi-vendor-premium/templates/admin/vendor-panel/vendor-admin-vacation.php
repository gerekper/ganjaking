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
?>
<div class="wrap yith-vendor-admin-wrap" id="vendor-details">
    <form method="post" action="<?php echo apply_filters( 'yith_wcmv_vendor_panel_form_action', admin_url( 'admin.php' ) ); ?>" enctype="multipart/form-data">
        <h3><?php _e( 'Seller vacation info', 'yith-woocommerce-product-vendors' ) ?></h3>

        <input type="hidden" name="update_vendor_id" value="<?php echo $vendor->id ?>" />
        <input type="hidden" name="action" value="yith_admin_save_fields" />
        <input type="hidden" name="page" value="<?php echo ! empty( $_GET['page'] ) ? $_GET['page'] : '' ?>" />
        <input type="hidden" name="tab" value="<?php echo ! empty( $_GET['tab'] ) ? $_GET['tab'] : '' ?>" />

        <?php echo wp_nonce_field( 'yith_vendor_admin_update', 'yith_vendor_admin_update_nonce', true, false ) ?>

        <div class="form-field">
            <label for="vacation_message"><?php  _e( 'Vacation message:', 'yith-woocommerce-product-vendors' ) ?></label>
            <textarea id="vacation_message" name="yith_vendor_data[vacation_message]" rows="10" cols="50" style="width: 400px;" class="regular-text"><?php echo esc_textarea( stripslashes( $vendor->vacation_message ) ) ?></textarea>
            <br/>
            <span class="description"><?php _e( "Add here the vacation message.", 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <div class="form-field vacation-date">
            <h3><?php _e( 'Vacation date', 'yith-woocommerce-product-vendors' ) ?></h3>
            <span class="vacation-date-field">
                <label for="vacation-start-date"><?php _e( 'Start date', 'yith-woocommerce-product-vendors' ); ?></label>
                <input style="width:120px;" type="text" class="date-picker-field" name="yith_vendor_data[vacation_start_date]" id="vacation-start-date" value="<?php echo ! empty( $vendor->vacation_start_date ) ? date_i18n( 'Y-m-d', strtotime( $vendor->vacation_start_date ) ) : ''; ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
            </span>
            <span class="vacation-date-field">
                <label for="vacation-end-date"><?php _e( 'End date', 'yith-woocommerce-product-vendors' ); ?></label>
                <input style="width:120px;" type="text" class="date-picker-field" name="yith_vendor_data[vacation_end_date]" id="vacation-end-date" value="<?php echo ! empty( $vendor->vacation_end_date ) ? date_i18n( 'Y-m-d', strtotime( $vendor->vacation_end_date ) ) : ''; ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
            </span>
        </div>

         <div class="form-field">
            <label for="vacation_selling"><?php _e( 'Selling option', 'yith-woocommerce-product-vendors' ); ?></label>
            <select name="yith_vendor_data[vacation_selling]" id="vacation_selling" style="width:250px;">
                <option value="disabled" <?php selected( 'disabled', $vendor->vacation_selling )?>><?php _e( 'Prevent sales temporarily', 'yith-woocommerce-product-vendors' )?></option>
                <option value="enabled" <?php selected( 'enabled', $vendor->vacation_selling )?>><?php _e( 'Keep selling anyway', 'yith-woocommerce-product-vendors' )?></option>
            </select>
        </div>

        <div class="submit">
            <input name="Submit" type="submit" class="button-primary" value="<?php echo esc_attr( __( 'Save Vacation Settings', 'yith-woocommerce-product-vendors' ) ) ?>" />
        </div>
    </form>
</div>
