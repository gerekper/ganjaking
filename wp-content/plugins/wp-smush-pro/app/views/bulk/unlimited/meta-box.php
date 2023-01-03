<?php
/**
 * Bulk Smush unlimited meta box.
 *
 * @since 3.12.0
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * We will hide this box when start bulk-smush,
 *
 * @see background-process.js, bulk-smush.js.
 */
?>
<h3 class="sui-box-title">
	<?php esc_html_e( 'You can now Bulk Smush unlimited images at once  🎉 ', 'wp-smushit' ); ?>
</h3>
<div class="sui-box-content">
	<p>
		<?php esc_html_e( 'We’ve now removed the 50 image compression limit so you can enjoy unlimited image optimization! See for yourself above. Looking for more free features? Try the free WPMU DEV plan, which includes Smush, plus a whole suite of other plugins and site management tools.', 'wp-smushit' ); ?>
	</p>
	<span type="button" class="sui-button" data-modal-open="smush-wpmudev-free-dialog"
	data-modal-close-focus="smush-box-bulk-unlimited"
	data-modal-mask="true"
	data-esc-close="true"
	data-modal-animated="true">
		<?php esc_html_e( 'learn more about wpmu dev free', 'wp-smushit' ); ?>
	</span>
	<span class="sui-button-icon notice-dismiss smush-dismiss-notice-button sui-tooltip sui-tooltip-top-right" data-tooltip="<?php esc_html_e( 'Dismiss', 'wp-smushit' ); ?>">
		<span class="sui-icon-close" aria-hidden="true"></span>
	</span>
</div>
