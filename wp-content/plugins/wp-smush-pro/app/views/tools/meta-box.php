<?php
/**
 * Tools meta box.
 *
 * @since 3.2.1
 * @package WP_Smush
 *
 * @var bool  $detection      Detection settings.
 * @var int   $backups_count
 *
 * @var Smush\App\Abstract_Page $this  Page.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

do_action( 'wp_smush_render_setting_row', 'detection', $detection );

?>
<div class="sui-box-settings-row <?php echo WP_Smush::is_pro() ? '' : 'sui-disabled'; ?>">
	<div class="sui-box-settings-col-1">
		<span class="<?php echo WP_Smush::is_pro() ? 'sui-settings-label' : 'sui-settings-label-with-tag'; ?>">
			<?php esc_html_e( 'Bulk restore', 'wp-smushit' ); ?>
			<?php if ( ! WP_Smush::is_pro() ) : ?>
				<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'wp-smushit' ); ?></span>
			<?php endif; ?>
		</span>
		<span class="sui-description">
			<?php
				esc_html_e( 'Made a mistake? Use this feature to restore your image thumbnails to their original state.', 'wp-smushit' );
			?>
		</span>
	</div>

	<div class="sui-box-settings-col-2">
		<button type="button" class="sui-button sui-button-ghost" onclick="WP_Smush.restore.init()" <?php disabled( ! $backups_count ); ?>>
			<i class="sui-icon-undo" aria-hidden="true"></i>
			<?php esc_html_e( 'Restore Thumbnails', 'wp-smushit' ); ?>
		</button>
		<span class="sui-description">
			<?php
			printf( /* translators: %1$s - a tag, %2$s - closing a tag */
				wp_kses( 'Note: This feature uses your original image uploads to regenerate thumbnails. If you have “%1$sSmush my original full size images%2$s” enabled, we can still restore your thumbnails, but the quality will reflect your compressed original image. ', 'wp-smushit' ),
				'<a href="' . esc_url( $this->get_url( 'smush-bulk' ) ) . '#original-label">',
				'</a>'
			);
			?>
		</span>
		<span class="sui-description">
			<?php
			printf( /* translators: %1$s - a tag, %2$s - closing a tag */
				esc_html__( 'Please note, that you need to have “%1$sStore a copy of my small originals%2$s” option enabled to bulk restore the images. ', 'wp-smushit' ),
				'<a href="' . esc_url( $this->get_url( 'smush-bulk' ) ) . '#backup-label">',
				'</a>'
			)
			?>
		</span>
	</div>
</div>