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
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

wc_print_notices();

?>
<div>
    <p class="form-row form-row-wide"><?php printf( esc_html__( 'If you don\'t want to receive any more review reminders, please retype your email address: %s', 'yith-woocommerce-review-reminder' ), '<b>' . urldecode( base64_decode( $_GET['email'] ) ) . '</b>' ) ?></p>
    <p class="form-row form-row-wide">
        <label for="account_email"><?php esc_html_e( 'Email address', 'yith-woocommerce-review-reminder' ); ?> <span class="required">*</span></label>
        <input type="email" class="input-text" name="account_email" id="account_email" />
    </p>
    <p class="form-row form-row-wide">
        <button type="button" class="button ywrr-unsubscribe"><?php esc_html_e( 'Unsubscribe', 'yith-woocommerce-review-reminder' ); ?></button>
        <input type="hidden" name="account_id" id="account_id" value="<?php echo urldecode( base64_decode( $_GET['id'] ) ); ?>" />
        <input type="hidden" name="email_hash" id="email_hash" value="<?php echo $_GET['email']; ?>" />
        <input type="hidden" name="email_type" id="email_type" value="ywrr_unsubscribe" />
    </p>
</div>
<p style="display: none;" class="return-to-shop form-row form-row-wide"><a class="button wc-backward" href="<?php echo get_home_url(); ?>"><?php esc_html_e( 'Back To Home Page', 'yith-woocommerce-review-reminder' ) ?></a></p>
