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
<?php $tax_label = get_option( 'yith_vat_label', __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ) ); ?>
<?php YITH_Vendors()->admin->add_upload_field( 'table', $vendor->header_image ) ?>
<?php YITH_Vendors()->admin->add_upload_field( 'table', $vendor->avatar, 'avatar', __( 'Avatar', 'yith-woocommerce-product-vendors' ) ) ?>

<tr class="form-field yith-choosen">
    <th scope="row" valign="top">
        <label for="key_user"><?php _e( 'Vendor Shop Owner', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <?php yit_add_select2_fields( $shop_owner_args ); ?>
        <br />
        <span class="description"><?php _e( 'User that can manage products in this shop and view sale reports.', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field yith-choosen">
    <th scope="row" valign="top">
        <label for="yith_vendor_admins"><?php _e( 'Vendor Shop Admins', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <?php yit_add_select2_fields( $shop_admins_args ); ?>
        <br />
        <span class="description"><?php _e( 'User that can manage products in this vendor shop and view sale reports.', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field yith-choosen">
    <th scope="row" valign="top">
        <h4><?php _e( 'Contact information :', 'yith-woocommerce-product-vendors' ) ?></h4>
    </th>
</tr>

<tr class="form-field yith-choosen">
    <th scope="row" valign="top">
        <label for="yith_vendor_location"><?php _e( 'Location', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>

    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[location]" id="yith_vendor_location" placeholder="<?php _e( "Store address. e.g.: MyStore S.A. Avenue MyStore 55, 1800 Vevey, Switzerland", 'yith-woocommerce-product-vendors' ); ?>" value="<?php echo $vendor->location ?>" /><br />
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_store_email"><?php _e( 'Store email', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[store_email]" id="yith_vendor_store_email" value="<?php echo $vendor->store_email ?>" /><br />
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_telephone"><?php _e( 'Telephone', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[telephone]" id="yith_vendor_telephone" value="<?php echo $vendor->telephone ?>" /><br />
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_vat"><?php echo $tax_label; ?></label>
    </th>
    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[vat]" id="yith_vendor_vat" value="<?php echo $vendor->vat ?>" /><br />
        <span class="description"><?php printf( esc_html_x( "Vendor's %s", "[Admin option description]: Vendor's VAT/SSN", 'yith-woocommerce-product-vendors' ), $tax_label ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_legal_notes"><?php echo apply_filters( 'yith_wcmv_company_legal_notes_field_title', _x( 'Company legal notes', 'Admin Option', 'yith-woocommerce-product-vendors' ) ); ?></label>
    </th>
    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[legal_notes]" id="yith_vendor_legal_notes" value="<?php echo $vendor->legal_notes ?>" /><br />
        <span class="description"><?php echo apply_filters( 'yith_wcmv_company_legal_notes_field_description', _x( 'Insert company legal notes (e.g. Managing Directors, Court of registration, Commercial registration number, ecc.)', 'Admin Option', 'yith-woocommerce-product-vendors' ) ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th>
        <?php $socials = $vendor->socials ?>
        <h4><?php _e( 'Social profile:', 'yith-woocommerce-product-vendors' ) ?></h4>
    </th>
</tr>

<?php $social_fields = ! empty( $social_fields ) ? $social_fields : array(); ?>
<?php foreach( $social_fields as $social => $social_args ) : ?>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_social_<?php echo $social ?>"><?php echo $social_args['label'] ?></label>
    </th>

    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[socials][<?php echo $social ?>]" id="yith_vendor_social_<?php echo $social ?>" value="<?php echo isset( $socials[ $social ] ) ? $socials[ $social ] : '' ?>" placeholder="http://" /><br />
    </td>
</tr>

<?php endforeach; ?>

<tr class="form-field">
    <th>
        <h4><?php _e( 'Payments:', 'yith-woocommerce-product-vendors' ) ?></h4>
    </th>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_paypal_email"><?php _e( 'PayPal email address', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[paypal_email]" id="yith_vendor_paypal_email" value="<?php echo $vendor->paypal_email ?>" /><br />
        <br />
        <span class="description"><?php _e( 'Vendor\'s PayPal email address where profits will be delivered.', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_bank_account"><?php _e( 'Bank Account (IBAN/BIC)', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <input type="text" class="regular-text" name="yith_vendor_data[bank_account]" id="yith_vendor_bank_account" value="<?php echo $vendor->bank_account ?>" /><br />
        <br />
        <span class="description"><?php _e( 'Vendor\'s IBAN/BIC bank account', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_enable_selling"><?php _e( 'Enable sales', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <?php $enable_selling = 'yes' == $vendor->enable_selling ? true : false; ?>
        <input type="checkbox" name="yith_vendor_data[enable_selling]" id="yith_vendor_enable_selling" value="yes" <?php checked( $enable_selling )?> /><br />
        <br />
        <span class="description"><?php _e( 'Enable or disable product sales.', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_skip_review"><?php _e( 'Skip admin review', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <?php $skip_review = 'yes' == $vendor->skip_review ? true : false; ?>
        <input type="checkbox" name="yith_vendor_data[skip_review]" id="yith_vendor_skip_review" value="yes" <?php checked( $skip_review )?> /><br />
        <br />
        <span class="description"><?php _e( 'Allow vendors to add products without admin review', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_enable_featured_products"><?php _e( 'Enable Featured products management', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <input type="checkbox" name="yith_vendor_data[featured_products]" id="yith_vendor_skip_review" value="yes" <?php checked( 'yes', $vendor->featured_products )?> /><br />
        <br />
        <span class="description"><?php _e( 'Allow vendors to manage featured products', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_enable_selling"><?php _e( 'Commission:', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <input type="number" class="regular-text" name="yith_vendor_data[commission]" id="yith_vendor_commission" value="<?php echo esc_attr( $vendor->get_commission() * 100 ); ?>" min="0" max="100" step="<?php echo $step ?>" /> %<br/>

        <br />
        <span class="description"><?php _e( 'Percentage of the total sale price that this vendor receives.', 'yith-woocommerce-product-vendors' ); ?></span>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_registration_date"><?php _e( 'Registration date:', 'yith-woocommerce-product-vendors' ); ?></label>
    </th>
    <td>
        <?php echo $vendor->get_registration_date( 'display' ) ?>
    </td>
</tr>
