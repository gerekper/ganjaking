<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Unsubscribe page shortcode template
 *
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

wc_print_notices();

?>
<div>
	<p><?php printf( esc_html( __( 'If you don\'t want to receive any more abandoned cart reminders, please retype your email address: %s', 'yith-woocommerce-recover-abandoned-cart' ) ), '<b>' . esc_html( urldecode( $_GET['customer'] ) ) . '</b>' ); ?></p>
	<p class="form-row form-row-wide">
		<label for="account_email"><?php esc_html_e( 'Email address', 'yith-woocommerce-recover-abandoned-cart' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text" name="account_email" id="account_email" />
	</p>
	<p class="form-row form-row-wide">
		<button type="button" class="button ywrac-unsubscribe"><?php esc_html_e( 'Unsubscribe', 'yith-woocommerce-recover-abandoned-cart' ); ?></button>
		<input type="hidden" name="email_hash" id="email_hash" value="<?php echo esc_attr( $_GET['customer'] ); ?>" />
		<input type="hidden" name="email_type" id="email_type" value="ywrac_unsubscribe" />

	</p>
</div>
<p style="display: none;" class="return-to-shop form-row form-row-wide"><a class="button wc-backward" href="<?php echo esc_url( get_home_url() ); ?>"><?php esc_html_e( 'Return To Home Page', 'yith-woocommerce-recover-abandoned-cart' ); ?></a></p>
