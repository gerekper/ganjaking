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

$product_title  = ! empty( $product ) ? $product->get_title() : '';
$title          = sprintf( __( 'Report an abuse for product %s', 'yith-woocommerce-product-vendors' ), $product_title );
$vendor_info    = $vendor->is_valid() ? sprintf( __( 'in vendor shop %s', 'yith-woocommerce-product-vendors'), $vendor->name ) : '';
$vendor_id      = $vendor->is_valid() ? $vendor->id : 0;

?>

<div id="yith-wpv-abuse-report-link">
    <a href="#yith-wpv-abuse-report" id="yith-wpv-abuse" rel="abuseFormPrettyPhoto[report_vendor_abuse]">
        <small><?php echo $abuse_text ?></small>
    </a>
</div>

<div id="yith-wpv-abuse-report" style="display: none;">
    <h3 class="yith-wpv-abuse-report-title"><?php echo apply_filters( 'yith-wpv-report-abuse-title', $title . ' ' . $vendor_info ) ?></h3>
    <form action="#" method="post" id="report-abuse" class="report-abuse-form">
        <input type="text" class="input-text " name="report_abuse[name]" value="<?php echo $current_user['display_name']; ?>" placeholder="<?php _e( 'Name', 'yith-woocommerce-product-vendors' ) ?>"  required/>
        <input type="email" class="input-text " name="report_abuse[email]" value="<?php echo $current_user['user_email']; ?>" placeholder="<?php _e( 'Email', 'yith-woocommerce-product-vendors' ) ?>"  required/>
        <textarea name="report_abuse[message]" rows="5" placeholder="<?php _e( 'Leave a message explaining the reasons for your abuse report', 'yith-woocommerce-product-vendors' ) ?>" required></textarea>
        <input type="hidden" name="report_abuse[spam]" value="" class="report_abuse_anti_spam"/>
        <input type="hidden" name="report_abuse[vendor_id]" value="<?php echo $vendor_id ?>" />
        <input type="hidden" name="report_abuse[product_id]" value="<?php echo yit_get_base_product_id( $product ) ?>" />
        <input type="hidden" name="report_abuse[subject]" value="<?php echo $title . ' ' . $vendor_info ?> " />
        <input type="hidden" name="action" value="send_report_abuse" />
        <input type="submit" class="submit-report-abuse <?php echo $button_class ?>" name="report_abuse[submit]" value="<?php echo $submit_label ?>" />
        <?php wp_nonce_field( 'yith_vendor_quick_info_submitted', 'yith_vendor_quick_info_submitted' ); ?>
    </form>
</div>