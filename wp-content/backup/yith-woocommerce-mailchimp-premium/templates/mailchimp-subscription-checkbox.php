<?php
/**
 * Subscription checkbox template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

?>

<label class="yith_wcmc_subscribe_me_label">
	<input type="checkbox" name="yith_wcmc_subscribe_me" id="yith_wcmc_subscribe_me" value="yes" <?php checked( $checkbox_checked ); ?>/>
	<?php echo wp_kses_post( $checkbox_label ); ?>
</label>
