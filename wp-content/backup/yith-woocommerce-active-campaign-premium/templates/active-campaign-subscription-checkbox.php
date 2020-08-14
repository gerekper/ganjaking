<?php
/**
 * Subscription checkbox template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="yith_wcac_subscribe_me_checkbox">
	<p>
	<label>
		<input type="hidden" name="yith_wcac_subscribe_me_enabled" value="yes" />
		<input type="checkbox" name="yith_wcac_subscribe_me" id="yith_wcac_subscribe_me" <?php checked( $checkbox_checked ); ?>/>
		<?php echo $checkbox_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</label>
	</p>
</div>
