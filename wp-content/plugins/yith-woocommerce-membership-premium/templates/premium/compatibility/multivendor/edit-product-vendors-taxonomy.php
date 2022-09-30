<?php
/*
 * Template for Edit product vendors taxonomy fields
 */
?>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="yith_vendor_manage_membership_plans"><?php esc_html_e( 'Manage membership plans', 'yith-woocommerce-membership' ); ?></label>
    </th>
    <td>
        <?php $manage_membership_plans = 'yes' == $vendor->manage_membership_plans ? true : false; ?>
        <input type="checkbox" name="yith_vendor_data[manage_membership_plans]" id="yith_vendor_manage_membership_plans" value="yes" <?php checked( $manage_membership_plans ) ?> /><br/>
        <br/>
        <span class="description"><?php esc_html_e( 'Allow vendors to manage membership plans', 'yith-woocommerce-membership' ); ?></span>
    </td>
</tr>