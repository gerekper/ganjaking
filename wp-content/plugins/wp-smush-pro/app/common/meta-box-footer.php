<?php
/**
 * Footer meta box, common to one or more modules.
 *
 * @since 3.2.0
 * @package WP_Smush
 *
 * @var \Smush\App\Abstract_Page $this
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$current_tab = $this->get_slug();
$button_msg  = 'smush-bulk' === $current_tab ? '' : __( 'Saving changes...', 'wp-smushit' );
$button_text = __( 'Save changes', 'wp-smushit' );

/**
 * Filter to enable/disable submit button in integration settings.
 *
 * @param bool $show_submit Should show submit?
 */
$disabled = 'smush-integrations' === $current_tab ? apply_filters( 'wp_smush_integration_show_submit', false ) : false;

if ( 'smush-cdn' === $current_tab && ! WP_Smush::get_instance()->core()->mod->cdn->get_status() ) {
	$button_text = __( 'Save & Activate', 'wp-smushit' );
	$button_msg  = __( 'Activating CDN...', 'wp-smushit' );
}
?>

<div class="sui-actions-right">
	<?php if ( 'smush-integrations' === $current_tab || 'smush-bulk' === $current_tab ) : ?>
		<span class="sui-field-prefix">
			<?php esc_html_e( 'Smush will automatically check for any images that need re-smushing.', 'wp-smushit' ); ?>
		</span>
	<?php endif; ?>

	<button type="submit" class="sui-button sui-button-blue" id="save-settings-button" aria-live="polite" <?php disabled( $disabled, false, false ); ?>>
		<span class="sui-button-text-default">
			<span class="sui-icon-save" aria-hidden="true"></span> <?php echo esc_html( $button_text ); ?>
		</span>

		<span class="sui-button-text-onload">
			<span class="sui-icon-loader sui-loading" aria-hidden="true"></span>
			<?php echo esc_html( $button_msg ); ?>
		</span>
	</button>
</div>