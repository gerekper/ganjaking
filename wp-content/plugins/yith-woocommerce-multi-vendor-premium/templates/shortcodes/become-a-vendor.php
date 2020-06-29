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
    exit; // Exit if accessed directly
}

$user                = get_user_by( 'id', get_current_user_id() );
$firstname           = ! empty( $_POST['vendor-owner-firstname'] ) ? sanitize_text_field( $_POST['vendor-owner-firstname'] ) : '';
$lastname            = ! empty( $_POST['vendor-owner-lastname'] ) ? sanitize_text_field( $_POST['vendor-owner-lastname'] ) : '';
$store_name          = ! empty( $_POST['vendor-name'] ) ? sanitize_text_field( $_POST['vendor-name'] ) : '';
$store_location      = ! empty( $_POST['vendor-location'] ) ? sanitize_text_field( $_POST['vendor-location'] ) : '';
$paypal_email        = ! empty( $_POST['vendor-paypal-email'] ) ? sanitize_email( $_POST['vendor-paypal-email'] ) : '';
$store_telephone     = ! empty( $_POST['vendor-telephone'] ) ? sanitize_text_field( $_POST['vendor-telephone'] ) : '';
$vat                 = ! empty( $_POST['vendor-vat'] ) ? sanitize_text_field( $_POST['vendor-vat'] ) : '';
$store_email         = apply_filters( 'yith_wcmv_pre_store_email', '' );
$address_placeholder = __( "Store address. e.g.: MyStore S.A. Avenue MyStore 55, 1800 Vevey, Switzerland", 'yith-woocommerce-product-vendors' );
$store_name_label = apply_filters( 'yith_wcmv_vendor_admin_settings_store_name_label', __( 'Store name', 'yith-woocommerce-product-vendors' ) );
$store_email_label = apply_filters( 'yith_wcmv_vendor_admin_settings_store_email_label', __( 'Store email', 'yith-woocommerce-product-vendors' ) );
if( ! empty( $_POST['vendor-email'] ) ){
    $store_email = sanitize_email( $_POST['vendor-email'] );
}

elseif( ! empty( $store_email ) && $user instanceof WP_User ){
    $store_email = $user->user_email;
}

$terms_and_conditions_page_id = get_option( 'yith_wpv_terms_and_conditions_page_id', 0 );
$a_start = $a_end = '';

if ( $terms_and_conditions_page_id ) {
	$a_start = sprintf( '<a href="%s" target="_blank">', esc_url( get_permalink( $terms_and_conditions_page_id ) ) );
	$a_end   = "</a>";
}

$terms_and_conditions_link = sprintf( '%s%s%s', $a_start, _x( 'terms &amp; conditions', '[Part of]: I have read and accept the...', 'yith-woocommerce-product-vendors' ), $a_end );
$terms_and_conditions_text = sprintf( '%s %s', _x( 'I&rsquo;ve read and accept the', '[Part of]: I have read and accept the...', 'yith-woocommerce-product-vendors' ), $terms_and_conditions_link );
$terms_and_conditions_text = apply_filters( 'yith_wcmv_terms_and_conditions_for_vendors_text', $terms_and_conditions_text );
?>

<div id="yith-become-a-vendor" class="woocommerce shortcodes">
    <?php if( function_exists( 'wc_print_notices' ) ){ wc_print_notices(); }; ?>
    <form method="post" class="register">
        <p class="form-row form-row-wide">
            <label for="vendor-name"><?php echo $store_name_label . ' *';?></label>
            <input type="text" class="input-text yith-required" name="vendor-name" id="vendor-name" value="<?php echo $store_name ?>">
        </p>

        <p class="form-row form-row-wide">
            <label for="vendor-location"><?php _e( 'Address *', 'yith-woocommerce-product-vendors' )?></label>
            <input type="text" class="input-text yith-required" name="vendor-location" id="vendor-location" value="<?php echo $store_location ?>" placeholder="<?php echo apply_filters( 'yith_wcmv_address_location_placeholder', $address_placeholder ) ; ?>">
        </p>

        <p class="form-row form-row-wide">
            <label for="vendor-email"><?php echo $store_email_label . ' *';?></label>
            <input type="text" class="input-text yith-required" name="vendor-email" id="vendor-email" value="<?php echo $store_email ?>">
        </p>

        <?php if( $is_paypal_email_enabled ) : ?>
            <p class="form-row form-row-wide">
                <?php $pp_email_field_required =  $is_paypal_email_required ? '*' : ''; ?>
                <label for="vendor-paypal-email">
                    <?php _e( 'PayPal email', 'yith-woocommerce-product-vendors' ); ?>
                    <?php echo $pp_email_field_required; ?>
                </label>
                <input type="text" class="input-text <?php echo $is_paypal_email_required ? 'yith-required' : '' ?>" name="vendor-paypal-email" id="vendor-paypal-email" value="<?php echo $paypal_email ?>">
            </p>
        <?php endif; ?>

        <p class="form-row form-row-wide">
            <label for="vendor-telephone"><?php _e( 'Telephone *', 'yith-woocommerce-product-vendors' )?></label>
            <input type="text" class="input-text yith-required" name="vendor-telephone" id="vendor-telephone" value="<?php echo $store_telephone ?>">
        </p>

        <p class="form-row form-row-wide">
            <?php $vat_field_required =  $is_vat_require ? '*' : ''; ?>
            <?php $vat_ssn_string = get_option( 'yith_vat_label', __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ) ); ?>
            <label for="vendor-vat"><?php echo $vat_ssn_string . ' ' . $vat_field_required ?></label>
            <input type="text" class="input-text <?php echo $is_vat_require ? 'yith-required' : '' ?>" name="vendor-vat" id="vendor-vat" value="<?php echo $vat ?>">
        </p>

        <?php if( $is_terms_and_conditions_require ) : ?>
            <p class="form-row form-row-wide last-child">
                <input type="checkbox" class="input-checkbox yith-required" name="vendor-terms" <?php checked( apply_filters( 'yith_wcmv_terms_is_checked_default', isset( $_POST['vendor-terms'] ) ), true ); ?> id="vendor-terms" required />
                <label for="vendor-terms" class="checkbox">
                    <?php echo $terms_and_conditions_text; ?> <span class="required">*</span>
                </label>
                <input type="hidden" name="terms-field" value="1" />
            </p>
        <?php endif; ?>

        <p class="form-row">
            <?php wp_nonce_field( 'woocommerce-register' ); ?>
            <input type="button" id="yith-become-a-vendor-submit" class="<?php apply_filters( 'yith_wpv_become_a_vendor_button_class', 'button' ) ?>" name="register" value="<?php echo $become_a_vendor_label; ?>" />
            <input type="hidden" id="yith-vendor-register" name="vendor-register" value="1">
            <input type="hidden" id="vendor-antispam" name="vendor-antispam" value="">
        </p>
    </form>
</div>