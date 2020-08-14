<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

?>
<div class="form-field yith-choosen">
    <label for="yith_vendor_owner"><?php _e( 'Vendor Shop Owner', 'yith-woocommerce-product-vendors' ); ?></label>
    <?php yit_add_select2_fields( $shop_owner_args ); ?>
</div>

<div class="form-field">
    <label for="yith_vendor_paypal_email"><?php _e( 'PayPal email address', 'yith-woocommerce-product-vendors' ); ?></label>
    <input type="text" class="regular-text" name="yith_vendor_data[paypal_email]" id="yith_vendor_paypal_email" value="" /><br />
    <span class="description"><?php _e( 'Vendor\'s PayPal email address where profits will be delivered.', 'yith-woocommerce-product-vendors' ); ?></span>
</div>

<div class="form-field">
    <label for="yith_vendor_vat">
        <?php echo $tax_label; ?>
    </label>
    <input type="text" class="regular-text" name="yith_vendor_data[vat]" id="yith_vendor_vat" value="" /><br />
    <span class="description"><?php printf( '%s %s.', __( 'Vendor\'s', 'yith-woocommerce-product-vendors' ), $tax_label ); ?></span>
</div>

<div class="form-field">
    <label for="yith_vendor_legal_notes">
        <?php echo apply_filters( 'yith_wcmv_company_legal_notes_field_title', _x( 'Company legal notes', 'Admin Option', 'yith-woocommerce-product-vendors' ) ); ?>
    </label>
    <input type="text" class="regular-text" name="yith_vendor_data[legal_notes]" id="yith_vendor_legal_notes" value="" /><br />
    <span class="description"><?php echo apply_filters( 'yith_wcmv_company_legal_notes_field_description', _x( 'Insert company legal notes (e.g. Managing Directors, Court of registration, Commercial registration number, ecc.)', 'Admin Option', 'yith-woocommerce-product-vendors' ) ); ?></span>
</div>

<div class="form-field">
    <label class="yith_vendor_enable_selling_label" for="yith_vendor_enable_selling"><?php _e( 'Enable sales', 'yith-woocommerce-product-vendors' ); ?></label>
    <input type="checkbox" name="yith_vendor_data[enable_selling]" id="yith_vendor_enable_selling" value="yes" checked /><br />
    <span class="description"><?php _e( 'Enable or disable product sales.', 'yith-woocommerce-product-vendors' ); ?></span>
</div>

<div class="form-field">
    <?php $skip_review = get_option( 'yith_wpv_vendors_option_skip_review' ); ?>
    <label class="yith_vendor_skip_revision_label" for="yith_vendor_skip_revision"><?php _e( 'Skip admin review', 'yith-woocommerce-product-vendors' ); ?></label>
    <input type="checkbox" name="yith_vendor_data[skip_review]" id="yith_vendor_enable_selling" value="yes" <?php checked( 'yes', $skip_review ) ?>/><br />
    <span class="description"><?php _e( 'Allow vendors to add products without admin review', 'yith-woocommerce-product-vendors' ); ?></span>
</div>

<div class="form-field">
    <?php $enable_featured_products = get_option( 'yith_wpv_vendors_option_featured_management' ); ?>
    <label class="yith_vendor_enable_featured_products_label" for="yith_vendor_enable_featured_products"><?php _e( 'Enable Featured products management', 'yith-woocommerce-product-vendors' ); ?></label>
    <input type="checkbox" name="yith_vendor_data[featured_products]" id="yith_vendor_featured_products" value="yes" <?php checked( 'yes', $enable_featured_products ) ?>/><br />
    <span class="description"><?php _e( 'Allow vendors to manage featured products', 'yith-woocommerce-product-vendors' ); ?></span>
</div>

<div class="form-field">
    <div class="yith-vendor-commission">
        <label class="yith_vendor_commission_label" for="yith_vendor_commission"><?php _e( 'Commission', 'yith-woocommerce-product-vendors' ); ?></label>
        <input type="number" class="regular-text" name="yith_vendor_data[commission]" id="yith_vendor_commission" value="<?php echo esc_attr( $commission * 100 ); ?>" min="0" max="100" step="<?php echo $step; ?>" /> %<br/>
    </div>
    <span class="description"><?php _e( 'Percentage of the total sale price that this vendor receives', 'yith-woocommerce-product-vendors' ); ?></span>
</div>