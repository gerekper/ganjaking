<?php
/**
 * Settings meta box.
 *
 * @package WP_Smush
 *
 * @var array $basic_features    Basic features list.
 * @var bool  $cdn_enabled       CDN status.
 * @var array $grouped_settings  Grouped settings that can be skipped.
 * @var array $settings          Settings values.
 * @var bool  $backup_exists     Number of attachments with backups.
 */

use Smush\Core\Settings;

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<?php if ( WP_Smush::is_pro() && $cdn_enabled && Settings::can_access( 'bulk' ) ) : ?>
	<div class="sui-notice sui-notice-info">
		<div class="sui-notice-content">
			<div class="sui-notice-message">
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<p><?php esc_html_e( 'Your images are currently being served via the WPMU DEV CDN. Bulk smush will continue to operate as per your settings below and is treated completely separately in case you ever want to disable the CDN.', 'wp-smushit' ); ?></p>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php
foreach ( $grouped_settings as $name ) {
	// If not bulk settings - skip.
	if ( ! in_array( $name, $grouped_settings, true ) ) {
		continue;
	}

	$can_access_pro        = $this->settings->can_access_pro_field( $name );
	$is_pro_field          = $this->settings->is_pro_field( $name );
	$is_upsell_field       = $this->settings->is_upsell_field( $name );
	$is_disabled_field     = ( $is_upsell_field || $is_pro_field ) && ! $can_access_pro;
	$is_pro_but_not_upsell = $is_pro_field && ! $is_upsell_field;
	// Only show pro upsell field on Bulk Smush page to avoid upselly UI.
	if ( $is_pro_but_not_upsell && ! $can_access_pro ) {
		continue;
	}
	$value = $is_disabled_field || empty( $settings[ $name ] ) ? false : $settings[ $name ];

	// Show settings option.
	do_action( 'wp_smush_render_setting_row', $name, $value, $is_disabled_field, $is_upsell_field );
}

// Hook after general settings.
do_action( 'wp_smush_after_basic_settings' );
?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<span class="<?php echo WP_Smush::is_pro() ? 'sui-settings-label' : 'sui-settings-label-with-tag'; ?>">
			<?php esc_html_e( 'Bulk restore', 'wp-smushit' ); ?>
		</span>
		<span class="sui-description">
			<?php
			esc_html_e( 'Made a mistake? Use this feature to restore your image thumbnails to their original state.', 'wp-smushit' );
			?>
		</span>
	</div>

	<div class="sui-box-settings-col-2">
		<button type="button" class="sui-button sui-button-ghost wp-smush-restore" onclick="WP_Smush.restore.init()" <?php disabled( ! $backup_exists ); ?>>
			<i class="sui-icon-undo" aria-hidden="true"></i>
			<?php esc_html_e( 'Restore Thumbnails', 'wp-smushit' ); ?>
		</button>
		<span class="sui-description">
			<?php
			printf( /* translators: %1$s - strong tag, %2$s - closing strong tag */
				wp_kses( 'This feature regenerates thumbnails using your original uploaded images. If %1$sCompress original images%2$s is enabled, your thumbnails can still be regenerated, but the quality will be impacted by the compression of your uploaded images.', 'wp-smushit' ),
				'<strong>',
				'</strong>'
			);
			?>
		</span>

		<div class="sui-notice" style="margin-top: 10px">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
					<p>
						<?php esc_html_e( 'Note: Backup original images must be enabled in order to bulk restore your images.', 'wp-smushit' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>