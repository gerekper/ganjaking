<?php
/**
 * Tutorials header under Bulk Smush meta box.
 *
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$notice = sprintf(
	/* translators: 1. opening 'a' tag linking to the tutorials tab, 2. closing 'a' tag. */
	__( 'The widget has been removed. Smush tutorials can still be found in the %1$sTutorials tab%2$s any time.', 'wp-smushit' ),
	'<a href="' . esc_url( $this->get_page_url() ) . '&view=tutorials">',
	'</a>'
);
?>

<h3 class="sui-box-title">
	<?php esc_html_e( 'Tutorials', 'wp-smushit' ); ?>
</h3>

<div class="sui-actions-right">
	<button
		id="wp-smush-dismiss-tutorials-button"
		class="sui-button-icon sui-tooltip"
		data-tooltip="<?php esc_html_e( 'Hide Tutorials', 'wp-smushit' ); ?>"
		data-notice="<?php echo esc_attr( $notice ); ?>"
		data-notice-close="<?php esc_attr_e( 'Close this notice', 'wp-smushit' ); ?>"
	>
		<i class="sui-icon-close sui-sm" aria-hidden="true"></i>
		<span class="sui-screen-reader-text"><?php esc_html_e( 'Hide Tutorials', 'wp-smushit' ); ?></span>
	</button>
</div>