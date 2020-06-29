<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var YITH_Commission $commission
 * @var YITH_Vendor $vendor
 * @var WC_Product $product
 * @var WC_Order $order
 * @var array $item
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$store_name_label = apply_filters( 'yith_wcmv_vendor_admin_settings_store_name_label', __( 'Store name', 'yith-woocommerce-product-vendors' ) );
$store_email_label = apply_filters( 'yith_wcmv_vendor_admin_settings_store_email_label', __( 'Store email', 'yith-woocommerce-product-vendors' ) );
?>
		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Owner:', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;">
                <?php
                $owner_info = '';
                /** @var $owner WP_User */
                $href = add_query_arg( array( 'user_id' => $owner->get( 'ID' ) ), admin_url( 'user-edit.php' ) );
                if( ! empty( $owner->user_firstname ) || ! empty( $owner->user_lastname ) ){
	                $owner_info = $owner->user_firstname . ' ' . $owner->user_lastname;
                }

                else {
                    $owner_info = $owner->user_email;
                }

                printf( '<a href="%s">%s</a>', $href, $owner_info )

                ?><
                /td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $store_name_label ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $vendor->name ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Location', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $vendor->location ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $store_email_label; ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $vendor->store_email ?></td>
		</tr>

		<tr>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php _e( 'Telephone', 'yith-woocommerce-product-vendors' ) ?></td>
			<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $vendor->telephone ?></td>
		</tr>