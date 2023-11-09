<?php
/**
 * Progress bar block.
 *
 * @package WP_Smush
 *
 * @var integer $count          Total number of images to smush.
 * @var string  $background_in_processing_notice
 * @var bool $background_processing_enabled
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="sui-notice sui-notice-warning wp-smush-recheck-images-notice-warning">
	<div class="sui-notice-content">
		<div class="sui-notice-message">
			<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
			<p>
				<span>
					<?php esc_html_e( 'Some images might need to be rechecked to ensure statistical data is accurate.', 'wp-smushit' ); ?>
				</span>
				<a href="#" class="wp-smush-trigger-background-scan">
					<?php esc_html_e( 'Re-check Now', 'wp-smushit' ); ?>
				</a>
			</p>
		</div>
		<div class="sui-notice-actions"><button class="sui-button-icon" type="button"><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-screen-reader-text"><?php esc_html_e( 'Close this notice', 'wp-smushit' ); ?></span></button></div>
	</div>
</div>

<div class="sui-notice sui-notice-success wp-smush-recheck-images-notice-success sui-hidden">
	<div class="sui-notice-content">
		<div class="sui-notice-message">
			<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
			<p>
				<?php
					/* translators: %s: Resume Bulk Smush link */
					printf( esc_html__( 'Image re-check complete. %s', 'wp-smushit' ), '<a href="#" class="wp-smush-trigger-bulk-smush">' . esc_html__( 'Resume Bulk Smush', 'wp-smushit' ) . '</a>' );
				?>
			</p>
		</div>
		<div class="sui-notice-actions"><button class="sui-button-icon" type="button"><span class="sui-icon-check" aria-hidden="true"></span><span class="sui-screen-reader-text"><?php esc_html_e( 'Close this notice', 'wp-smushit' ); ?></span></button></div>
	</div>
</div>