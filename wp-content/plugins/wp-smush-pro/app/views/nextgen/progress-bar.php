<?php
/**
 * Progress bar block.
 *
 * @package WP_Smush
 *
 * @var integer $count Total number of images to smush.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="wp-smush-bulk-progress-bar-wrapper sui-hidden">
	<div class="sui-notice sui-notice-warning sui-hidden"></div>

	<div id="wp-smush-running-notice" class="sui-notice">
		<div class="sui-notice-content">
			<div class="sui-notice-message">
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<p><?php esc_html_e( 'Bulk smush is currently running. You need to keep this page open for the process to complete.', 'wp-smushit' ); ?></p>
			</div>
		</div>
	</div>

	<div class="sui-progress-block sui-progress-can-close">
		<div class="sui-progress">
			<span class="sui-progress-icon" aria-hidden="true">
				<i class="sui-icon-loader sui-loading"></i>
			</span>
			<div class="sui-progress-text">
				<span class="wp-smush-images-percent">0%</span>
			</div>
			<div class="sui-progress-bar">
				<span class="wp-smush-progress-inner" style="width: 0%"></span>
			</div>
		</div>
		<button class="sui-progress-close wp-smush-cancel-bulk" type="button">
			<?php esc_html_e( 'Cancel', 'wp-smushit' ); ?>
		</button>
		<button class="sui-progress-close sui-button-icon sui-tooltip wp-smush-all sui-hidden" type="button" data-tooltip="<?php esc_html_e( 'Resume scan.', 'wp-smushit' ); ?>">
			<i class="sui-icon-play"></i>
		</button>
	</div>

	<div class="sui-progress-state">
		<span class="sui-progress-state-text">
			<span>0</span>/<span class="wp-smush-total-count"><?php echo absint( $count ); ?></span> <?php esc_html_e( 'images smushed', 'wp-smushit' ); ?>
		</span>
	</div>

	<div id="bulk-smush-resume-button" class="sui-hidden">
		<a class="wp-smush-all sui-button wp-smush-started wp-smush-resume-bulk-smush">
			<i class="sui-icon-play" aria-hidden="true"></i>
			<?php esc_html_e( 'Resume', 'wp-smushit' ); ?>
		</a>
	</div>
</div>