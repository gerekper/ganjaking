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
        <h3><?php _e( 'Manual Payments Information', 'yith-woocommerce-product-vendors' ) ?></h3>

        <input type="hidden" name="update_vendor_id" value="<?php echo $vendor->id ?>" />
        <input type="hidden" name="action" value="yith_admin_save_fields" />
        <input type="hidden" name="page" value="<?php echo ! empty( $_GET['page'] ) ? $_GET['page'] : '' ?>" />
        <input type="hidden" name="tab" value="<?php echo ! empty( $_GET['tab'] ) ? $_GET['tab'] : '' ?>" />

        <?php echo wp_nonce_field( 'yith_vendor_admin_payments', 'yith_vendor_admin_payments_nonce', true, false ) ?>

        <?php if( YITH_Vendors()->is_paypal_email_enabled() ) :  ?>
            <div class="form-field">
                <label for="yith_vendor_paypal_email"><?php _e( 'PayPal email address', 'yith-woocommerce-product-vendors' ); ?></label>
                <input type="text" class="regular-text" name="yith_vendor_data[paypal_email]" id="yith_vendor_paypal_email" value="<?php echo $vendor->paypal_email ?>" /><br />
                <span class="description"><?php _e( 'Your PayPal email address.', 'yith-woocommerce-product-vendors' ); ?></span>
            </div>
        <?php endif; ?>

        <div class="form-field">
            <label for="yith_vendor_bank_account"><?php _e( 'Bank Account (IBAN/BIC)', 'yith-woocommerce-product-vendors' ); ?></label>
            <input type="text" class="regular-text" name="yith_vendor_data[bank_account]" id="yith_vendor_bank_account" value="<?php echo $vendor->bank_account ?>" /><br />
            <span class="description"><?php _e( 'Your IBAN/BIC bank account', 'yith-woocommerce-product-vendors' ); ?></span>
        </div>

        <?php do_action( 'yith_wcmv_vendor_panel_payments', $args ); ?>

         <div class="submit">
            <input name="Submit" type="submit" class="button-primary" value="<?php echo esc_attr( __( 'Save Payments Information', 'yith-woocommerce-product-vendors' ) ) ?>" />
        </div>
    </form>
</div>